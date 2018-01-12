<?php
/**
 * Created by PhpStorm.
 * User: GoOse
 * Date: 12.01.2018
 * Time: 19:12
 */

$images = array("../../Images/Anlage01.jpg",
    "../../Images/Anlage02.jpg",
    "../../Images/Anlage03.jpg",
    "../../Images/Anlage04.jpg");
$imageWidth = 728;
$stepPercentage = 90 / count($images);
?>
<div id="slideshow">
  <div class="slide-wrapper">

<?php foreach ($images as $image): ?>

    <div class="slide"><img src = "<?=$image?>" /></div>


<?php endforeach; ?>
</div>
</div>


<style>
    body {
        font-family: Helvetica, sans-serif;
        padding: 5%;
        text-align: center;
    }

    #slideshow {
        overflow: hidden;
        height: 510px;
        width: <?=$imageWidth?>px;
        margin: 0 auto;
    }

    .slide-wrapper {
        width: <?=($imageWidth * count($images))?>px;
        -webkit-animation: slide <?=count($images) * 3?>s ease infinite;
    }

    .slide {
        float: left;
        height: 510px;
        width: <?=$imageWidth?>px;
    }


    @-webkit-keyframes slide {
        <?=$stepPercentage?>% {margin-left: 0px;}
        <?php for ($i = 1; $i < count($images); $i++): ?>

        <?=($i * $stepPercentage)?>% {margin-left: -<?=$i * $imageWidth?>px;}
        <?=(($i + 1) * $stepPercentage)?>% {margin-left: -<?=$i * $imageWidth?>px;}

        <?php endfor; ?>
    }
</style>