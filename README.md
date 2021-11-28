# publicinfobanjir.water.gov.my scrapper

Public Infobanjir system works by collecting real-time rainfall water level data from nearly 200 hydrological stations across the country. Hydrological data from each station is transmitted to the Telemetry Database / servers in each state and then transmitted to Infobanjir. Initially, the infobanjir system operations focused or monitored and used internally, i.e: rainfall information and water levels would be monitored by DID officers only.

## Getting started

```
use PublicInfoBanjir\RainAndRiver;

RainAndRiver::getRainLevel($state);
RainAndRiver::getRiverLevel($state);

$state list:

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

```
