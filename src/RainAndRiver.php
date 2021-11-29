<?php
declare(strict_types=1);

namespace PublicInfoBanjir;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class RainAndRiver {

  static public function getRainLevel(string $state, bool $raw = false)
  {
    try {
      // https://publicinfobanjir.water.gov.my/hujan/data-hujan/?state=KEL&lang=en
      $client = new Client(['base_uri' => 'http://publicinfobanjir.water.gov.my']);
      $res = $client->get('/wp-content/themes/shapely/agency/searchresultrainfall.php', ['query' => ['state' => $state, 'district' => 'ALL', 'station' => 'ALL', 'language' => '1', 'loginStatus' => '0'], 'debug' => false]);
      $html = '<!DOCTYPE html><html><body>'.(string) $res->getBody().'</body></html>';
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
              $results['headers'] = $headers;
            } else if ($idx1 == 3) {
              foreach($child1->childNodes as $idx2 => $child2) {
                $val = trim($child2->textContent);
                if (strlen($val) > 0)
                  array_push($thresholds, $val);
              }
              $results['daily'] = $thresholds;
            }
          }
        } else if ($idx === 1) {
          $data = [];
          $index = 0;
          $daily = [];
          $temp_result = [];
          foreach($child->childNodes as $idx1 => $child1) {
            $val = trim($child1->textContent);
            if (strlen($val) > 0) {
              if ($index <= 4) {
                $temp_result[preg_replace("/[^a-zA-Z0-9]+/", "", $headers[$index] ?? $index)] = $val;
              } else if ($index >= 5 && $index <= 10) {
                array_push($daily, $child1->textContent);
              } else {
                if ($index == 11)
                  $temp_result[preg_replace("/[^a-zA-Z]+/", "", $headers[6] ?? 'rainfall_from_Midnight')] = $val;
                else if ($index == 12)
                  $temp_result[preg_replace("/[^a-zA-Z0-9]+/", "", $headers[7] ?? 'total_1_hour_now')] = $val;
              }
              $index++;
              if ($index == 13) {
                $temp_result[preg_replace("/[^a-zA-Z]+/", "", $headers[5] ?? 'daily')] = $daily;
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

  static public function getRiverLevel(string $state, bool $raw = false)
  {
    try {
      // https://publicinfobanjir.water.gov.my/aras-air/data-paras-air/?state=KEL&lang=en
      $client = new Client(['base_uri' => 'http://publicinfobanjir.water.gov.my']);
      $res = $client->get('/aras-air/data-paras-air/aras-air-data/', ['query' => ['state' => $state, 'district' => 'ALL', 'station' => 'ALL', 'lang' => 'en'], 'debug' => false]);
      $html = '<!DOCTYPE html><html><body>'.(string) $res->getBody().'</body></html>';
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
              $results['headers'] = $headers;
            } else if ($idx1 == 4) {
              foreach($child1->childNodes as $idx2 => $child2) {
                $val = trim($child2->textContent);
                if (strlen($val) > 0)
                  array_push($thresholds, $val);
              }
              $results['thresholds'] = $thresholds;
            }
          }
        } else if ($idx === 1) {
          foreach($child->childNodes as $idx1 => $child1) {
            if ($idx1 > 0) {
              $data = [];
              foreach ($child1->childNodes as $idx2 => $child2) {
                if ($idx2 < 8)
                  $data[preg_replace("/[^a-zA-Z0-9]+/", "", $headers[$idx2] ?? (string) $idx2)] = trim($child2->textContent);
                else
                  $data[preg_replace("/[^a-zA-Z0-9]+/", "", $thresholds[$idx2 - 8] ?? (string) $idx2)] = trim($child2->textContent);
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
