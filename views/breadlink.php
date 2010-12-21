<?php foreach ($contents as $data) :?>
    <a href="<?=$data["url"];?>"><?=$data["title"];?></a> 
    <?=$sep;?>
<?php endforeach;?>
    <?=$currentpage;?>
