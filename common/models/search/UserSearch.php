<?php

namespace common\models\search;

use common\models\User;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * UserSearch represents the model behind the search form about `common\models\User`.
 */
class UserSearch extends User
{

    public $search_line;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['search_line'], 'required'],
            [['search_line'], 'string', 'min' => 3],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }


    /**
     * @return ActiveDataProvider
     */
    public function search()
    {
        $query = User::find()->where(['status' => User::STATUS_ACTIVE]);

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'username', $this->search_line])
            ->andFilterWhere(['!=', 'id', Yii::$app->user->identity->getId()]);
//            ->andFilterWhere(['like', 'first_name', $this->search_line])
//            ->andFilterWhere(['like', 'last_name', $this->search_line]);

        return $dataProvider;
    }
}
