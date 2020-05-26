<?php

namespace mix8872\menu\controllers;

use mix8872\menu\classes\Event;
use mix8872\menu\Module;
use Yii;
use mix8872\menu\models\Menu;
use mix8872\menu\models\MenuMove;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * MenuController implements the CRUD actions for Menu model.
 */
class DefaultController extends \yii\web\Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'move-item' => ['POST'],
                    'add-item' => ['POST', 'GET'],
                ],
            ],
        ];
    }

    /**
     * Lists all Menu models.
     * @param null $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionIndex($id = null)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Menu::find()->where(['depth' => 0]),
        ]);

        $model = null;
        $items = null;

        if ($id) {
            $model = $this->findModel($id);
            $items = $model->getDescendants()->indexBy('id')->all();

            if (Yii::$app->request->isPost) {
                $post = Yii::$app->request->post();

                if ($model->load($post) && $model->save()) {
                    $err = $this->_batchUpdate($items, $post, ['name', 'url', 'description']);
                    if ($err['result']) {
                        $event = new Event(['model' => $model]);
                        $this->module->trigger(Module::EVENT_AFTER_UPDATE, $event);
                        Yii::$app->getSession()->setFlash('success', Yii::t('menu', 'Меню успешно обновлено'));
                        return $this->redirect(['index', 'id' => $model->id]);
                    } else {
                        Yii::$app->session->setFlash('error', Yii::t('menu', 'При обновлении пунктов меню произошла ошибка:') . " {$err['message']}");
                    }
                } else {
                    Yii::$app->getSession()->setFlash('error', Yii::t('menu', 'При обновлении меню произошла ошибка'));
                }
                $this->redirect(['index', 'id' => $id]);
            }
        }

        return $this->render('index', compact('dataProvider', 'model', 'items'));
    }

    /**
     * Creates a new Menu model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws \yii\base\Exception
     */
    public function actionCreate()
    {
        $model = new Menu([
            'name' => Yii::t('menu', 'Новое меню'),
            'code' => Yii::$app->security->generateRandomString(6),
            'url' => '#'
        ]);

        if ($model->makeRoot()->save()) {
            $event = new Event(['model' => $model]);
            $this->module->trigger(Module::EVENT_AFTER_CREATE, $event);
            return $this->redirect(['index', 'id' => $model->id]);
        }

        Yii::$app->session->setFlash('error', Yii::t('menu', 'Ошибка добавления меню'));
        return $this->redirect(['index']);
    }

    public function actionAddItem($id, $type = 0)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $parent = Menu::findOne($id);
        $post = Yii::$app->request->post();
        $newItem = new Menu([
            'name' => isset($post['name']) ? trim(strip_tags($post['name'])) : Yii::t('menu', 'Новый пункт меню'),
            'type' => trim(strip_tags($type)),
            'code' => Yii::$app->security->generateRandomString(11)
        ]);
        if ($newItem->appendTo($parent)->save(false)) {
            $event = new Event(['model' => $newItem]);
            $this->module->trigger(Module::EVENT_AFTER_ADD_ITEM, $event);
            $this->module->trigger(Module::EVENT_AFTER_UPDATE, $event);
            return [
                'success' => true,
                'item' => trim($this->renderPartial('_menu-item', [
                    'isParent' => false,
                    'item' => $newItem,
                    'isNewItem' => true
                ]))
            ];
        }
        return ['success' => false];
    }

    public function actionMoveItem($id)
    {
        $items = new MenuMove();
        $items->load(Yii::$app->getRequest()->getBodyParams(), '');
        if (!$items->validate()) {
            return $items;
        }

        $model = $this->findModel($id);
        $event = new Event(['model' => $model]);

        if ($items->prev_id > 0) {
            $parentModel = $this->findModel($items->prev_id);
            if ($parentModel->isRoot()) {
                $result = $model->appendTo($parentModel)->save();
                $this->module->trigger(Module::EVENT_AFTER_SORT, $event);
                $this->module->trigger(Module::EVENT_AFTER_UPDATE, $event);
                return $result;
            } else {
                $result = $model->insertAfter($parentModel)->save();
                $event = new Event(['model' => $model]);
                $this->module->trigger(Module::EVENT_AFTER_SORT, $event);
                $this->module->trigger(Module::EVENT_AFTER_UPDATE, $event);
                return $result;
            }
        } elseif ($items->next_id > 0) {
            $parentModel = $this->findModel($items->next_id);
            $result = $model->insertBefore($parentModel)->save();
            $this->module->trigger(Module::EVENT_AFTER_SORT, $event);
            $this->module->trigger(Module::EVENT_AFTER_UPDATE, $event);
            return $result;
        } elseif ($items->parent_id > 0) {
            $parentModel = $this->findModel($items->parent_id);
            $result = $model->appendTo($parentModel)->save();
            $this->module->trigger(Module::EVENT_AFTER_SORT, $event);
            $this->module->trigger(Module::EVENT_AFTER_UPDATE, $event);
            return $result;
        }

        return false;
    }

    public function actionDeleteItem($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $item = Menu::findOne($id);
        if ($item && $item->deleteWithChildren()) {
            $event = new Event(['model' => $item]);
            $this->module->trigger(Module::EVENT_AFTER_DELETE_ITEM, $event);
            $this->module->trigger(Module::EVENT_AFTER_UPDATE, $event);
            return [
                'success' => true
            ];
        }
        return [
            'success' => false
        ];
    }

    /**
     * Deletes an existing Menu model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->deleteWithChildren();
        $event = new Event(['model' => $model]);
        $this->module->trigger(Module::EVENT_AFTER_DELETE, $event);
        return $this->redirect(['index']);
    }


//------------------------------------------------------------------

    private function _batchUpdate($models, $post, $fields = null)
    {
        $err = ['result' => true];
        $model = new \yii\base\Model();
        if ($model->loadMultiple($models, $post)) {
            if ($model->validateMultiple($models, $fields)) {
                foreach ($models as $key => $item) {
                    if (!$item->save()) $err = ['result' => false, 'message' => Yii::t('menu', 'Ошибка сохранения элемента ') . $key . ' - ' . $item->name];
                }
            } else {
                $err = ['result' => false, 'message' => Yii::t('menu', 'Ошибка валидации')];
            }
        } else {
            $err = ['result' => false, 'message' => Yii::t('menu', 'Ошибка данных')];
            error_log(print_r($models, 1));
        }
        return $err;
    }

    /**
     * Finds the Menu model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Menu the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Menu::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('menu', 'Меню не найдено'));
        }
    }
}
