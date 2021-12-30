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
      $js = '<script>function calculate(){var e=new URL(document.location.toString());e.searchParams.set("html",0),fetch(e.toString()).then(e=>e.json()).then(e=>{console.clear();const t={};e.data.forEach(e=>{var a=0;if(null!=e.DailyRainfall&&e.DailyRainfall.length>0){e.DailyRainfall.forEach(e=>{const t=parseFloat(e);t>=0&&(a+=t)}),null==t[e.District]&&(t[e.District]=0);var n=parseFloat(e.RainfallfromMidnight);n>=0&&(t[e.District]+=n),t[e.District]+=a}});var a=[];for(var n in t)a.push({name:n,value:t[n]});a.sort((e,t)=>e.value>t.value?-1:1);const l=new Date,o=l.getDate(),r=l.getMonth()+1;l.setTime(l.getTime()-5184e5);const i=l.getDate(),c=l.getMonth()+1;var s=document.createElement("ul");s.setAttribute("id","total_rainfall");var m=`\nTotal rainfall for 7 consecutive days(${o}/${r} - ${i}/${c}):\n`,u=document.createElement("h3");u.setAttribute("style","margin-left:4px;"),document.body.appendChild(u),u.innerHTML=m,a.forEach(e=>{var t=30-e.name.length;m+=`${e.name}${"-".repeat(t)}-> ${e.value.toFixed(2)}mm\n`;var a=document.createElement("li");s.appendChild(a),a.innerHTML=`${e.name} ${e.value.toFixed(2)}mm\n`}),document.body.appendChild(s),console.log(m)}).catch(e=>{console.error(e)})}calculate();</script>';
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
