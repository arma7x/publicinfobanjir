# publicinfobanjir.water.gov.my scrapper

Public Infobanjir system works by collecting real-time rainfall water level data from nearly 200 hydrological stations across the country. Hydrological data from each station is transmitted to the Telemetry Database / servers in each state and then transmitted to Infobanjir. Initially, the infobanjir system operations focused or monitored and used internally, i.e: rainfall information and water levels would be monitored by DID officers only.


## Installation

```
"repositories": [
    {
        "url": "https://github.com/arma7x/publicinfobanjir.git",
        "type": "git"
    }
],
"require": {
    "arma7x/publicinfobanjir": "dev-master"
},

```

## Getting started

```
use PublicInfoBanjir\RainAndRiver;

// Rain data from https://publicinfobanjir.water.gov.my/hujan/data-hujan/?state=KEL&lang=en
RainAndRiver::getRainLevel($state, $html = false);

// River data from https://publicinfobanjir.water.gov.my/aras-air/data-paras-air/?state=KEL&lang=en
RainAndRiver::getRiverLevel($state, $html = false);

```

## Parameter

```
### $state
KDH => Kedah
PNG => Pulau Pinang
PRK => Perak
SEL => Selangor
WLH => Wilayah Persekutuan Kuala Lumpur
PTJ => Wilayah Persekutuan Putrajaya
NSN => Negeri Sembilan
MLK => Melaka
JHR => Johor
PHG => Pahang
TRG => Terengganu
KEL => Kelantan
SRK => Sarawak
SAB => Sabah
WLP => Wilayah Persekutuan Labuan

### $html
false json
true html

```

## Demo

```
// https://publicinfobanjir.water.gov.my/aras-air/data-paras-air/?state=KEL&lang=en
https://malaysiaapi.herokuapp.com/banjir/v1/river?state=KEL&html=0
https://malaysiaapi.herokuapp.com/banjir/v1/river?state=KEL&html=1

// https://publicinfobanjir.water.gov.my/hujan/data-hujan/?state=KEL&lang=en
https://malaysiaapi.herokuapp.com/banjir/v1/rain?state=KEL&html=0
https://malaysiaapi.herokuapp.com/banjir/v1/rain?state=KEL&html=1


```

## Extra(Total rainfall for 7 consecutive days):

```
var url = new URL(document.location.toString());
url.searchParams.set('html', 0);
fetch(url.toString())
.then((response) => {
  return response.json();
})
.then((json) => {
  console.clear();
  const district = {};
  json.data.forEach((d) => {
    var total = 0;
    if (d['DailyRainfall'] != null && d['DailyRainfall'].length > 0) {
      d['DailyRainfall'].forEach((v) => {
        const val = parseFloat(v);
        if (val >= 0) {
          total += val;
        }
      });
      if (district[d['District']] == null) {
        var today = parseFloat(d['RainfallfromMidnight']);
        district[d['District']] = today >= 0 ? today : 0;
      }
      district[d['District']] += total;
    }
  });
  const date = new Date();
  console.log(`Total rainfall for 7 consecutive days(${date.getDate()}/${s.getMonth() + 1} - ${date.getDate() - 6}/${s.getMonth() + 1}):`);
  for (var dis in district) {
    console.log(dis, `${district[dis].toFixed(2)}mm`);
  }
})
.catch((err) => {
  console.error(err);
});

```
