<?php
Yii::setAlias('@static', 'http://'.$_SERVER['HTTP_HOST'].'/static');
Yii::setAlias('@js', '@static/js');
Yii::setAlias('@css', '@static/css');
Yii::setAlias('@image', '@static/imgs');
Yii::setAlias('@metro', 'http://'.$_SERVER['HTTP_HOST'].'/metr_admin');