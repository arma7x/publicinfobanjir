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
      $js = '<script>function calculate(){var t=new URL(document.location.toString());t.searchParams.set("html",0),fetch(t.toString()).then(t=>t.json()).then(t=>{console.clear();const a={};t.data.forEach(t=>{var o=0;if(null!=t.DailyRainfall&&t.DailyRainfall.length>0){t.DailyRainfall.forEach(t=>{const a=parseFloat(t);a>=0&&(o+=a)}),null==a[t.District]&&(a[t.District]=0);var e=parseFloat(t.RainfallfromMidnight);e>=0&&(a[t.District]+=e),a[t.District]+=o}});const o=new Date,e=o.getDate(),l=o.getMonth()+1;o.setTime(o.getTime()-5184e5);const n=o.getDate(),i=o.getMonth()+1;for(var c in console.log(`Total rainfall for 7 consecutive days(${e}/${l} - ${n}/${i}):`),a)console.log(c,`${a[c].toFixed(2)}mm`)}).catch(t=>{console.error(t)})}</script>';
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
