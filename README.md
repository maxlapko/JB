JSON Builder for heavy struct for Yii (analog jbuilder(ruby))

## Requirements
    PHP version >= 5.3

## Getting started

Put files to protected/components directory

Import files:

```php
//protected/config/main.php

return array(
    'import' => array(
        //...
        'application.components.*',    
    ),
    //...
);
```

### Configuring Controllers

Extends controllers from JController

```php
//any json controller

class Items extends JController
{
    public function actionIndex()
    {
        $dataProvider=new CActiveDataProvider('Item');
        $this->render('index',array(
            'dataProvider'=>$dataProvider,
        ));
    }

    //or

    public function actionIndex()
    {
        $items = Item::model()->findAll();
        $this->renderJSON('index', array('items' => $items));
    }
} 
```

### Configuring Views

JSON view uses prefix _json.

Example:

    HTML template   index.php
    JSON template   index_json.php

All render variables available as JB::getVar(name)
 
```php
//view/items/index_json.php

echo JB::encode(function($json) {        
    $json->items(JB::getVar('dataProvider')->getData(), function($json, $item) {
        $json->set($item, 'id,name,price');
        $json->url = Yii::app()->createUrl('items/view', array('id' => $item->id));
        $json->comments = $item->comments;
        
        if (Yii::app()->user->isAdmin) {
            $json->adminInfo(function($json) {
                $json->approveUrl = Yii::app()->createUrl('items/approve', array('id' => $item->id));
            });        
        }
    });
    $json->totalCount = JB::getVar('dataProvider')->getTotalItemCount();
});
```
Return JSON

```javascript
{
    items: [{ 
        id: 1, name: "Test 1", price: 0.55, url: "/items/1", 
        comments: [{id: 2, item_id: 1, content: "dddd ..."}, {....}, ...] 
    }, ....],
    totalCount: 5,
    adminInfo: { //if admin
        approveUrl: "/items/approve/id/1"
    }    
}

```

### Other

Also all pages available as json
Example 

        http://your_host/items?_format=json
        http://your_host/items/1?_format=json

if json template not found, return echo CJSON::encode(renderData); 


