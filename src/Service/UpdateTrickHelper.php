<?php

namespace App\Service;

class UpdateTrickHelper {
    
    
    public function storeOldsUrls($figure)
    {
        $medias=$figure->getMedias();
        // Store the ancients URL to use in case form uis submitted without selected file
        $oldsUrl=[];
        foreach($medias as $media )
        {   
            if($media->getUrl() != null){
                //If the media have an url we store it
                array_push($oldsUrl,$media->getUrl());
                //Allow to generate a file for the FileType with the URL
                $media->setUrl(new File('C:\wamp64\www\SnowTricks/public'.$media->getUrl()));
            }
        }
    }
}
