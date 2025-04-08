<?php
    class YaCyRequest extends EngineRequest {
        public function get_request_url() {
            $query_encoded = str_replace("%22", "\"", urlencode($this->query));
            $results = array();

            $results_language = $this->opts->language;
            $number_of_results = $this->opts->number_of_results;

            $url = "https://yacy.nube-gran.de/yacysearch.json?query=$query_encoded";

            if (3 > strlen($results_language) && 0 < strlen($results_language))
                $url .= "&lr=lang_$results_language";

            if (3 > strlen($number_of_results) && 0 < strlen($number_of_results))
                $url .= "&maximumRecords=$number_of_results";

            if ($this->page)
                $url .= "&start=$start";

            return $url;
        }

        public function parse_results($response) {
            $results = array();
            $json = json_decode($response, true);

            if (!$json)
                return $results;

            if (!is_array($json["channels"]))
                return $results;

            $json = $json["channels"][0];

            if ($json["items"] && !is_array($json["items"]))
                return $results;

            foreach($json["items"] as $item) {
                $title = $item["title"];
                $url = $item["link"];
                $base_url = get_base_url($url);
                $description = $item["description"];

                array_push($results,
                    array(
                        "title" => htmlspecialchars($title),
                        "url" =>  htmlspecialchars($url),
                        // base_url is to be removed in the future, see #47
                        "base_url" => htmlspecialchars(get_base_url($url)),
                        "description" =>  $description == null ?
                                          TEXTS["result_no_description"] :
                                          htmlspecialchars($description)
                    )
                );
            }

            return $results;
        }

    }
?>
