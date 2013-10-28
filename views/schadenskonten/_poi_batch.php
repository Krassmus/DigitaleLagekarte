<li id="poi_<?= $poi->getId() ?>" class="poi_batch" style="background-image: url('<?= $assets_url.($poi['image'] ? "markers".$poi["image"] : "eal.svg") ?>');">
    <div class="title"><?= htmlReady($poi['title']) ?> (<?= $poi['shape'] ?>)</div>
</li>