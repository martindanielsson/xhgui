<?php
class Xhgui_WatchFunctions
{
    protected $_collection;

    public function __construct(MongoDB\Database $db)
    {
        $this->_collection = $db->watches;
    }

    /**
     * Save a value to the collection.
     *
     * Will do an insert or update depending
     * on the id field being present.
     *
     * @param array $data The data to save.
     * @return boolean
     */
    public function save($data)
    {
        if (empty($data['name'])) {
            return false;
        }

        if (!empty($data['removed']) && isset($data['_id'])) {
            $this->_collection->deleteOne(
                array('_id' => new \MongoDB\BSON\ObjectID($data['_id'])),
                array('w' => 1)
            );
            return true;
        }

        if (empty($data['_id'])) {
            $this->_collection->insertOne(
                $data,
                array('w' => 1)
            );
            return true;
        }


        $data['_id'] = new \MongoDB\BSON\ObjectID($data['_id']);
        $this->_collection->replaceOne(
            array('_id' => $data['_id']),
            $data,
            array('w' => 1)
        );
        return true;
    }

    /**
     * Get all the known watch functions.
     *
     * @return array Array of watch functions.
     */
    public function getAll()
    {
        $cursor = $this->_collection->find();
        return array_values(iterator_to_array($cursor));
    }

    /**
     * Truncate the watch collection.
     *
     * @return void
     */
    public function truncate()
    {
        $this->_collection->drop();
    }

}
