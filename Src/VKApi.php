<?php

class VKApi
{

    static public function sendAnswer($message)
    {
        global $peerID;
        $rand = rand(0000000000, 9999999999);
        $params = array(
            'random_id' => $rand,
            'message' => $message,
            'peer_id' => $peerID,
            'access_token' => Config::$apiToken,
            'v' => Config::$apiVer
        );
        $params = http_build_query($params);
        file_get_contents('https://api.vk.com/method/messages.send?'. $params);
        die();
    }

    static public function getCommentsCount()
    {
        $params = array(
            'owner_id' => "-".Config::$groupID,
            'access_token' => Config::$serviceKey,
            'v' => Config::$apiVer,
            "post_id" => Config::$postID,
            "count" => 1
        );
        $params = http_build_query($params);
        $result = file_get_contents('https://api.vk.com/method/wall.getComments?'. $params);
        $result = json_decode($result);
        if(!empty($result->response->count)){
            return $result->response->count;
        } else {
            return false;
        }
    }

    static public function getUploadUrl()
    {
        $params = array(
            'group_id' => Config::$groupID,
            'access_token' => Config::$apiToken,
            'v' => Config::$apiVer,
            'crop_x' => 150,
            'crop_y' => 0,
            'crop_x2' => 1440,
            'crop_y2' => 400,
        );
        $params = http_build_query($params);
        $result = file_get_contents('https://api.vk.com/method/photos.getOwnerCoverPhotoUploadServer?'. $params);
        $result = json_decode($result);
        if(!empty($result->response->upload_url)){
            return $result->response->upload_url;
        }
        return false;
    }

    static public function sendImage($uploadURL, $headScrPath)
    {
        if(file_exists($headScrPath."/Images/cover.png")) {
            $cFile = new CURLFile(realpath($headScrPath."/Images/cover.png"));
            $params = array(
                'group_id' => Config::$groupID,
                'access_token' => Config::$apiToken,
                'v' => Config::$apiVer,
                'photo' => $cFile
            );
            $ch = curl_init($uploadURL);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            $result = curl_exec($ch);
            curl_close($ch);
            $result = json_decode($result);
            if(!empty($result->hash && !empty($result->photo))){
                return $result;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    static public function saveCover($hash, $photo)
    {
        $params = array(
            'group_id' => Config::$groupID,
            'access_token' => Config::$apiToken,
            'v' => Config::$apiVer,
            'hash' => $hash,
            'photo' => $photo
        );
        $params = http_build_query($params);
        $result = file_get_contents('https://api.vk.com/method/photos.saveOwnerCoverPhoto?'. $params);
        $result = json_decode($result);
        if(!empty($result->response->images) && count($result->response->images) > 0){
            return true;
        }
        return false;
    }
}