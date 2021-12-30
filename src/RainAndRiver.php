<?php
declare(strict_types=1);

namespace PublicInfoBanjir;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class RainAndRiver {

  static public function getRainLevel(string $state = 'KEL', bool $raw = false)
  {
    try {
      // https://publicinfobanjir.water.gov.my/hujan/data-hujan/?state=KEL&lang=en
      $client = new Client(['base_uri' => 'http://publicinfobanjir.water.gov.my']);
      $res = $client->get('/wp-content/themes/shapely/agency/searchresultrainfall.php', ['query' => ['state' => $state, 'district' => 'ALL', 'station' => 'ALL', 'language' => '1', 'loginStatus' => '0'], 'debug' => false]);
      $html = '<!DOCTYPE html><html><body>'.(string) $res->getBody().'</body></html>';
      $html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
      $js = '<script>function calculate(){var t=new URL(document.location.toString());t.searchParams.set("html",0),fetch(t.toString()).then(t=>t.json()).then(t=>{console.clear();const e={};t.data.forEach(t=>{var a=0;if(null!=t.DailyRainfall&&t.DailyRainfall.length>0){t.DailyRainfall.forEach(t=>{const e=parseFloat(t);e>=0&&(a+=e)}),null==e[t.District]&&(e[t.District]=0);var n=parseFloat(t.RainfallfromMidnight);n>=0&&(e[t.District]+=n),e[t.District]+=a}});const a=new Date,n=a.getDate(),l=a.getMonth()+1;a.setTime(a.getTime()-5184e5);const i=a.getDate(),o=a.getMonth()+1;var r=document.createElement("ul");r.setAttribute("id","total_rainfall");var c=`\nTotal rainfall for 7 consecutive days(${n}/${l} - ${i}/${o}):\n`,s=document.createElement("h3");for(var d in s.setAttribute("style","margin-left:4px;"),document.body.appendChild(s),s.innerHTML=c,e){var m=30-d.length;c+=`${d}${"-".repeat(m)}-> ${e[d].toFixed(2)}mm\n`;var h=document.createElement("li");r.appendChild(h),h.innerHTML=`${d} ${e[d].toFixed(2)}mm\n`}document.body.appendChild(r),console.log(c)}).catch(t=>{console.error(t)})}calculate();</script>';
      $html = preg_replace('#<body(.*?)</body>#is', '<body$1'.$js.'</body>', $html);
      if ($raw)
        return $html;
      $crawler = new Crawler($html);
      $table = $crawler->filter('table');
      $headers = [];
      $thresholds = [];
      $results = [];
      foreach ($table->first()->children() as $idx => $child) {
        if ($idx === 0) {
          foreach($child->childNodes as $idx1 => $child1) {
            if ($idx1 == 2) {
              foreach($child1->childNodes as $idx2 => $child2) {
                $val = trim($child2->textContent);
                if (strlen($val) > 0)
                  array_push($headers, $val);
              }
              //$results['headers'] = $headers;
            } else if ($idx1 == 3) {
              foreach($child1->childNodes as $idx2 => $child2) {
                $val = trim($child2->textContent);
                if (strlen($val) > 0)
                  array_push($thresholds, $val);
              }
              //$results['daily'] = $thresholds;
            }
          }
        } else if ($idx === 1) {
          $h = ["No.","Station ID","Station","District","Last Updated","Daily Rainfall","Rainfall from Midnight","Total 1 Hour(Now)"];
          $data = [];
          $index = 0;
          $daily = [];
          $temp_result = [];
          foreach($child->childNodes as $idx1 => $child1) {
            $val = trim($child1->textContent);
            if (strlen($val) > 0) {
              if ($index <= 4) {
                $temp_result[preg_replace("/[^a-zA-Z0-9]+/", "", $h[$index])] = $val;
              } else if ($index >= 5 && $index <= 10) {
                array_push($daily, $child1->textContent);
              } else {
                if ($index == 11)
                  $temp_result[preg_replace("/[^a-zA-Z]+/", "", $h[6])] = $val;
                else if ($index == 12)
                  $temp_result[preg_replace("/[^a-zA-Z0-9]+/", "", $h[7])] = $val;
              }
              $index++;
              if ($index == 13) {
                $temp_result[preg_replace("/[^a-zA-Z]+/", "", $h[5])] = $daily;
                array_push($data, $temp_result);;
                $index = 0;
                $daily = [];
                $temp_result = [];
              }
            }
          }
          $results['data'] = $data;
        }
      }
      return $results;
    } catch(\Exception $e) {
      throw($e);
    }
  }

  static public function getRiverLevel(string $state = 'KEL', bool $raw = false)
  {
    try {
      // https://publicinfobanjir.water.gov.my/aras-air/data-paras-air/?state=KEL&lang=en
      $client = new Client(['base_uri' => 'http://publicinfobanjir.water.gov.my']);
      $res = $client->get('/aras-air/data-paras-air/aras-air-data/', ['query' => ['state' => $state, 'district' => 'ALL', 'station' => 'ALL', 'lang' => 'en'], 'debug' => false]);
      $html = '<!DOCTYPE html><html><body>'.(string) $res->getBody().'</body></html>';
      $html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
      if ($raw)
        return $html;
      $crawler = new Crawler($html);
      $table = $crawler->filter('table');
      $headers = [];
      $thresholds = [];
      $temp_result = [];
      $results = [];
      foreach ($table->first()->children() as $idx => $child) {
        if ($idx === 0) {
          foreach($child->childNodes as $idx1 => $child1) {
            if ($idx1 == 2) {
              foreach($child1->childNodes as $idx2 => $child2) {
                $val = trim($child2->textContent);
                if (strlen($val) > 0)
                  array_push($headers, $val);
              }
              //$results['headers'] = $headers;
            } else if ($idx1 == 4) {
              foreach($child1->childNodes as $idx2 => $child2) {
                $val = trim($child2->textContent);
                if (strlen($val) > 0)
                  array_push($thresholds, $val);
              }
              //$results['thresholds'] = $thresholds;
            }
          }
        } else if ($idx === 1) {
          $h = ["No.","Station ID","Station Name","District","Main Basin","Sub River Basin","Last Updated","Water Level (m)(Graph)","Threshold"];
          $t = ["Normal","Alert","Warning","Danger"];
          foreach($child->childNodes as $idx1 => $child1) {
            if ($idx1 > 0) {
              $data = [];
              foreach ($child1->childNodes as $idx2 => $child2) {
                if ($idx2 < 8)
                  $data[preg_replace("/[^a-zA-Z0-9]+/", "", $h[$idx2])] = trim($child2->textContent);
                else
                  $data[preg_replace("/[^a-zA-Z0-9]+/", "", $t[$idx2 - 8])] = trim($child2->textContent);
              }
              array_push($temp_result, $data);
            }
          }
        }
      }
      $results['data'] = $temp_result;
      return $results;
    } catch(\Exception $e) {
      throw($e);
    }
  }

}
