<?php

if (!empty($_GET['q'])) {
    
    $q = "q=" . urlencode($_GET['q']);
    
    if (preg_match("/^\d{9}/", $_GET['q'])) {
        $q = "q=bib:" . $_GET['q'];
    }
    
    $limit = 3;
    $start = 0;
    
    if (!empty($_GET['start'])) {
        $start = $_GET['start'];
    }
    
    $formats = "";//"+material-id:matBook";
    
    $url = "http://webservices.lib.harvard.edu/rest/hollis/search/mods/?$q$formats";

    $agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)';
    $ch = curl_init();

    // set URL and other appropriate options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_USERAGENT, $agent);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  

    // grab URL and pass it to the browser  

    $output = curl_exec($ch);
    curl_close($ch);

    $xml_response = new SimpleXMLElement($output);
    
    $total_results = $xml_response->totalResults[0];
    
    $to_out = array();
    $to_out['num_found'] = (int) $total_results;
    
    $docs = array();
    if (!empty($xml_response->resultSet->item)) {
        foreach ($xml_response->resultSet->item as $item) {
//            $ns_dc = $item->children('http://purl.org/dc/elements/1.1/');
            
//            if ($ns_dc->format == 'Book') {
                $to_out_item = array();
                
                $raw_title_article = (string) $item->mods->titleInfo->nonSort[0];
                $raw_title = (string) $item->mods->titleInfo->title[0];
                
                if (!empty($raw_title_article)) {
                    $to_out_item['title'] = $raw_title_article . trim(preg_replace('/\s+/', ' ', $raw_title));
                } else {
                    $to_out_item['title'] = trim(preg_replace('/\s+/', ' ', $raw_title));                    
                }
        
                $raw_creator = (string) $item->mods->name[0]->namePart[0];
                $to_out_item['creator'] = trim(preg_replace('/\s+/', ' ', $raw_creator));
        
                $raw_id_inst = (string) $item->mods->recordInfo[0]->recordIdentifier[0];
                $raw_id_inst = preg_replace('/-\d$/', ' ', $raw_id_inst);
                $to_out_item['id_inst'] = trim(preg_replace('/\s+/', ' ', $raw_id_inst));
                    
                $raw_isbn = (string) $item->mods->identifier[0];
                $raw_isbn = preg_replace("/\s.*/", "", $raw_isbn);
                $to_out_item['id_isbn'] = trim(preg_replace('/\s+/', ' ', $raw_isbn));
        
                $docs[] = $to_out_item;
//            }
        }
    }
    
    $sliced_docs = array_slice($docs, $start, $limit);
    
    $to_out['docs'] = $sliced_docs;
    
    print json_encode($to_out);
}

?>