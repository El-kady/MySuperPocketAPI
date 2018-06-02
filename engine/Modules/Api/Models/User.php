<?php

namespace Modules\Api\Models;

use \Core\Service\Service;
use \Core\System\BaseModel;
use \ReallySimpleJWT\Token;

class User extends BaseModel
{
    protected $table = "users";


    public function getJWT($user){

        Service::getLogger()->info("User logged",[$user['email']]);

        $tokenId    = base64_encode(random_bytes(32));
        $expireation   = date('c',strtotime('+1 year'));
        $serverName = Service::getConfig()->get("JWT_KEY");

        $secretKey = Service::getConfig()->get("JWT_KEY");
        Service::getLogger()->info("Into",[$tokenId,$expireation,$serverName,$secretKey]);

        try {
            $jwt = Token::getToken($tokenId, $secretKey, $expireation, $serverName);
            Service::getLogger()->info("User JWT",[$jwt]);
            return $jwt;
        } catch (\Exception $e) {
            Service::getSession()->add('feedback_negative', $e->getMessage());
        }

        return '';
    }

    public function login($email, $password)
    {

        if (!empty($email) && !empty($password)) {
            if (($user = $this->getRow($email, "email"))) {
                if (md5($password) == $user['password']) {
                    if (Service::getAuth()->setLogin($user)) {
                        return $user;
                    }
                } else {
                    Service::getSession()->add('feedback_negative', Service::getText()->get("EMAIL_OR_PASSWORD_WRONG"));
                }
            } else {
                Service::getSession()->add('feedback_negative', Service::getText()->get("EMAIL_OR_PASSWORD_WRONG"));
            }
        } else {
            Service::getSession()->add('feedback_negative', Service::getText()->get("FILL_ALL_FIELDS"));
        }
        return false;
    }

    public function register($data)
    {

        foreach ($data as $key => $value) {
            if (empty($value)) {
                Service::getSession()->add('feedback_negative', sprintf(Service::getText()->get("FIELD_IS_REQUIRED"), Service::getText()->get($key)));
            }
        }

        if (!empty($data["email"]) && !filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
            Service::getSession()->add('feedback_negative', Service::getText()->get("EMAIL_NOT_VALID"));
        }

        if (!empty($data["email"]) && $this->getRow($data["email"], "email")) {
            Service::getSession()->add('feedback_negative', Service::getText()->get("EMAIL_ALREADY_EXISTS"));
        }

        if (!empty($data["phone"]) && !filter_var($data["phone"], FILTER_VALIDATE_INT)) {
            Service::getSession()->add('feedback_negative', Service::getText()->get("PHONE_NOT_VALID"));
        }

        if (!empty($data["phone"]) && $this->getRow($data["phone"], "phone")) {
            Service::getSession()->add('feedback_negative', Service::getText()->get("PHONE_ALREADY_EXISTS"));
        }

        if (count(Service::getSession()->get('feedback_negative')) == 0) {
            $record = [
                "account_type" => 1,
                "name" => $data["name"],
                "email" => $data["email"],
                "phone_number" => $data["phone_number"],
                "password" => md5($data["password"]),
                "status" => 1
            ];
            if ($this->insert($record)) {
                return true;
            }
        }
        return false;

    }
    public function saveData($data)
    {

        Service::getForm()->fillTmp('user_edit', $data);

        $required = ["name","email"];

        if (!empty($data["password"])) {
            $required[] = "retype_password";
        }

        $record = [
            "name" => $data["name"],
            "email" => $data["email"],
            "country" => $data["country"],
            "gender" => $data["gender"],
        ];

        foreach ($required as $field) {
            if ($data[$field] == "") {
                Service::getSession()->add('feedback_negative', sprintf(Service::getText()->get("FIELD_IS_REQUIRED"), Service::getText()->get($field)));
            }
        }

        if (!empty($data["email"]) && !filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
            Service::getSession()->add('feedback_negative', Service::getText()->get("EMAIL_NOT_VALID"));
        }

        if (!empty($data["email"]) && $user_email_row = $this->getRow($data["email"], "email")) {
            if (Service::getAuth()->getUserId() != $user_email_row["id"]) {
                Service::getSession()->add('feedback_negative', Service::getText()->get("EMAIL_ALREADY_EXISTS"));
            }
        }

        if (!empty($data["retype_password"]) && $data["retype_password"] != $data["password"]) {
            Service::getSession()->add('feedback_negative', Service::getText()->get("PASSWORDS_NOT_MATCH"));
        }


        if ($data["user_photo"]["name"] != "") {
            try {
                Service::getUploader()->upload("user_photo", ["image"]);
                if (Service::getUploader()->isUploaded()) {
                    $record["user_photo"] = Service::getUploader()->getFile("physical_url");
                }
            } catch (\Exception $e) {
                Service::getSession()->add('feedback_negative', Service::getText()->get($e->getMessage()));
            }
        }

        if (count(Service::getSession()->get('feedback_negative')) == 0) {
            if ($data["password"] != "") {
                $record["password"] = md5($data["password"]);
            }
            if ($this->update($record,"id = :id",["id" => Service::getAuth()->getUserId()])) {
                return true;
            }
        }
        return false;

    }


}