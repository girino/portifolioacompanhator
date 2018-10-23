#!/bin/bash

DIR="${BASH_SOURCE%/*}"
if [[ ! -d "$DIR" ]]; then DIR="$PWD"; fi
. "$DIR/db_config.sh"

DATE=`/bin/date +%s`
TBNAME=quotes


DIGITS=12
FBTC="%"${DIGITS}".8f"
FALT="%"${DIGITS}".6f"
FBRL="%"$((DIGITS-2))".2f"

SEP_SIZE=58
function separator {
  printf %${SEP_SIZE}s |tr " " "$1"
  printf '\n'
}

POLO_TICKER=$(curl -q 'https://poloniex.com/public?command=returnTicker' 2>/dev/null)
#FOX_TICKER=$(curl -q 'https://api.blinktrade.com/api/v1/BRL/ticker?crypto_currency=BTC' 2>/dev/null)
BVL_TICKER=$(curl -q 'https://api.bitvalor.com/v1/ticker.json' 2>/dev/null)

DCR_LAST=$(echo "$POLO_TICKER" | jq '.BTC_DCR.last|tonumber')
ETH_LAST=$(echo "$POLO_TICKER" | jq '.BTC_ETH.last|tonumber')
BCH_LAST=$(echo "$POLO_TICKER" | jq '.BTC_BCH.last|tonumber')
LTC_LAST=$(echo "$POLO_TICKER" | jq '.BTC_LTC.last|tonumber')
#BTC_LAST=$(echo "$FOX_TICKER" | jq '.last')
BTC_LAST=$(echo "$BVL_TICKER" | jq '.ticker_1h.total.vwap')

SUM_DCR=$(echo "[$DCR_LAST, $BTC_LAST]" | jq '.[0] * .[1]')
SUM_ETH=$(echo "[$ETH_LAST, $BTC_LAST]" | jq '.[0] * .[1]')
SUM_BCH=$(echo "[$BCH_LAST, $BTC_LAST]" | jq '.[0] * .[1]')
SUM_LTC=$(echo "[$LTC_LAST, $BTC_LAST]" | jq '.[0] * .[1]')


echo "CREATE TABLE IF NOT EXISTS $DBNAME.$TBNAME (time int, BTCBRL double, DCRBTC double, ETHBTC double, BCHBTC double, LTCBTC double)" | mysql -u $USER -p$PASS $DBNAME
echo "INSERT INTO $DBNAME.$TBNAME VALUES ($DATE, $BTC_LAST, $DCR_LAST, $ETH_LAST, $BCH_LAST, $LTC_LAST);" | mysql -u $USER -p$PASS $DBNAME
