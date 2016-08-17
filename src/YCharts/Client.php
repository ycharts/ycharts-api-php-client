<?php
/**
  *This client serves as a basic model of how to interact with our api in php. It is
 * limited to our /companies endpoint and does not perform any client side validation
 * of inputs. For further information regarding the capabilities of the api,
 * please visit: https://ycharts.com/api/docs/
 */
class YChartsApiClient {

    public function __construct($api_key) {
        $this->base_url = 'https://ycharts.com/api/v3/';
        $this->api_key = $api_key;
    }

    /**
     * Get company infos for given companies and info_fields
     * @param companies array of symbols for companies (eg. ['AAPL', 'MSFT'])
     * @param info_fields array of requested info fields (eg. ['exchange'])
     * @return a php variable of the decoded JSON response of requested info fields for the companies
     */
    public function get_company_info($companies, $info_fields) {
        $companies = join(",", $companies);
        $info_fields = join(",", $info_fields);
        $url = $this->base_url."companies/$companies/info/$info_fields";
        $json_data = $this->_get_data($url);
        return json_decode($json_data);
    }

    /**
     * Get company data points for given companies, metrics, and date
     * @param companies array of symbols for companies (eg. ['AAPL', 'MSFT'])
     * @param metrics array of requested metrics (eg. ['price', 'pe_ratio'])
     * @param date_attr string in the YYYY-MM-DD format (if empty, it will retrieve current data point)
     * @return a php variable of the decoded JSON response of requested data points for the companies
     */
    public function get_company_data_point($companies , $metrics , $date_attr = NULL) {
        $companies = join(",", $companies);
        $metrics = join(",", $metrics);
        $url = $this->base_url."companies/$companies/points/$metrics";
        if ($date_attr) {
            $params = 'date='.urlencode($date_attr);
        }
        $json_data = $this->_get_data($url, $params);
        return json_decode($json_data);
    }

    /**
     * Get company data series for given companies, metrics, start_date, and end_date
     * @param companies array of symbols for companies (eg. ['AAPL', 'MSFT'])
     * @param metrics array of requested metrics (eg. ['price', 'pe_ratio'])
     * @param start_date string in the YYYY-MM-DD format representing start date of series
     * @param end_date string in the YYYY-MM-DD format representing end date of series
     * @return a php variable of the decoded JSON response of requested data series for the companies
     */
    public function get_company_data_timeseries($companies, $metrics , $start_date = NULL , $end_date = NULL) {
        $companies = join(",", $companies);
        $metrics = join(",", $metrics);
        $url = $this->base_url."companies/$companies/series/$metrics";

        $params = '';

        if ($start_date) {
            $params .= 'start_date='.urlencode($start_date);
        }

        if ($end_date) {
            $params .= '&end_date='.urlencode($end_date);
        }

        $json_data = $this->_get_data($url, $params);
        return json_decode($json_data);
    }

    /**
     * Get json response from server for the url string and params
     * @param url the requested url
     * @param params the query string parameters
     * @return JSON response from server for the requested url
     */
    public function _get_data($url, $params='') {

        $contentType = 'application/json';
        $charset= 'UTF-8';

        if($params){
            $url = $url.'?'.$params;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: '.$contentType.'; charset: '.$charset, "X-YCHARTSAUTHORIZATION: ".$this->api_key));
        return curl_exec($ch);
    }
}


/**
 *   Sample Code Using the API Client
 *   To use the data returned, check out http://php.net/manual/en/function.json-decode.php
 */

$api_key = ''; // Enter the Key here. See http://ycharts.com/accounts/my_account

$client = new YChartsApiClient($api_key);

var_dump($client->get_company_info(array('AAPL', 'MSFT'), array('exchange', 'industry')));

var_dump($client->get_company_data_point(array('AAPL', 'MSFT'), array('price', 'pe_ratio'), '2016-03-03'));

var_dump($client->get_company_data_timeseries(array('AAPL', 'MSFT'), array('price'), '2016-03-03', '2016-03-15'));

?>
