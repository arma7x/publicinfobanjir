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

// https://publicinfobanjir.water.gov.my/hujan/data-hujan/?state=KEL&lang=en
RainAndRiver::getRainLevel($state, $html = false);

// https://publicinfobanjir.water.gov.my/aras-air/data-paras-air/?state=KEL&lang=en
RainAndRiver::getRiverLevel($state, $html = false);

```

## Parameter:

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

## Example output:

```
// https://publicinfobanjir.water.gov.my/aras-air/data-paras-air/?state=KEL&lang=en
https://malaysiaapi.herokuapp.com/banjir/v1/river?state=KEL&html=0
https://malaysiaapi.herokuapp.com/banjir/v1/river?state=KEL&html=1

// https://publicinfobanjir.water.gov.my/hujan/data-hujan/?state=KEL&lang=en
https://malaysiaapi.herokuapp.com/banjir/v1/rain?state=KEL&html=0
https://malaysiaapi.herokuapp.com/banjir/v1/rain?state=KEL&html=1


```
