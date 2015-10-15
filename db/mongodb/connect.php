<?php

namespace limepie\db\mongodb;

class connect extends \MongoClient
{

    public function __construct($name)
    {

        $connect = \limepie\config::get("mongodb-server", $name);

        if (TRUE === isset($connect["host"])
            && TRUE === isset($connect["username"])
            && TRUE === isset($connect["password"])
            && TRUE === isset($connect["authSource"])) {
            parent::__construct(
                "mongodb://".$connect["username"].":".$connect["password"]."@".$connect["host"], [
                    'authSource' => $connect["authSource"]
                ]
            );
        } else {
            throw new exception("mongodb ". $name . " config를 확인하세요.");
        }

    }

    public function get($db, $collection, $query, $field=[])
    {
        if (array_key_exists('_id', $query)) {
            $query['_id'] = new \MongoId($query['_id']);
        }
        $result = parent::selectCollection($db, $collection)->findOne($query, $field);
        if (is_array($result) && array_key_exists('_id', $result)) {
            $result['id'] = $result['_id']->{'$id'};
        }
        return $result;
    }

    public function set($db, $collection, &$document)
    {
        try {
            $newDocument = $document;
            $result = parent::selectCollection($db, $collection)->save($newDocument);
            $document = $newDocument;
            return $result;
        } catch (\MongoException $e) {
            throw new exception($e);
        }
    }

    public function setId($db, $collection, $document)
    {
        if (self::set($db, $collection, $document)) {
            return $document['_id']->{'$id'};
        }
        return FALSE;
    }

    public function createIndex($db, $collection, $keys, $options=[])
    {
        return parent::selectCollection($db, $collection)->createIndex($keys, $options);
    }

    // public function update($db, $collection, $criteria, $newDocument)
    // {
    //     return parent::selectCollection($db, $collection)->update($criteria, $newDocument);
    // }

}
