<?php

namespace Api\Model;

use Api\Lib\SingletonTrait;

class RoomModel extends AbstractModel
{
    use SingletonTrait;

    /**
     * connect to table.
     *
     * @var string
     */
    protected $name = 'rooms';

    /**
     * [updateRoomInfo description].
     *
     * @param [type] &$room [description]
     *
     * @return [type] [description]
     */
    protected function updateRoomInfo(&$room)
    {
        $room->players = [];
        $room->viewers = [];
        $room->player_ids = json_decode($room->player_ids);
        $room->viewer_ids = json_decode($room->viewer_ids);
        $room->player_ids = $room->player_ids ? $room->player_ids : [];
        $room->viewer_ids = $room->viewer_ids ? $room->viewer_ids : [];

        // get player infor
        foreach ($room->player_ids as $uid) {
            $room->players[] = $this->model('User', function (UserModel $model) use ($uid) {
                return $model->table()->find($uid, ['id', 'username', 'email', 'fullname']);
            });
        }

        // get viewer infor
        foreach ($room->viewer_ids as $uid) {
            $room->viewers[] = $this->model('User', function (UserModel $model) use ($uid) {
                return $model->table()->find($uid, ['id', 'username', 'email', 'fullname']);
            });
        }

        return $room;
    }

    /**
     * [getRooms description].
     *
     * @return array list of rooms data
     */
    public function getEnabledRooms($id = null)
    {
        $query = $this->table()->where('enable', '=', true);
        if ($id !== null) {
            $query->where('id', '=', $id);
            if ($room = $query->first()) {
                return $this->updateRoomInfo($room);
            }
            return null;
        }
        $rooms = $query->get();
        foreach ($rooms as &$room) {
            $room = $this->updateRoomInfo($room);
        }

        return $rooms;
    }

    /**
     * [updateRoom description]
     * @param  [type] $id     [description]
     * @param  array  $values [description]
     * @return [type]         [description]
     */
    public function updateRoom($id, $values = [])
    {
        return (boolean)$this->table()
            ->where('id', '=', $id)
            ->update($values);
    }
}
