<?php
namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\User;

class UserSearch extends Model
{
    public $id;
    public $username;
    public $email;
    public $role;
    public $status;

    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
            [['username', 'email', 'role'], 'safe'],
        ];
    }

    public function search($params)
    {
        $query = User::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 10],
            'sort' => ['defaultOrder' => ['id' => SORT_ASC]],
        ]);

        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['id' => $this->id])
              ->andFilterWhere(['status' => $this->status])
              ->andFilterWhere(['role' => $this->role])
              ->andFilterWhere(['like', 'username', $this->username])
              ->andFilterWhere(['like', 'email', $this->email]);

        return $dataProvider;
    }
}
