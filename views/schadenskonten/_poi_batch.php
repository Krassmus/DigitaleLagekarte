<li id="poi_<?= $poi->getId() ?>" class="poi_batch" style="background-image: url('<?= $assets_url ?>markers/eal.svg');">
    <?= htmlReady($poi['title']) ?> (<?= $poi['shape'] ?>)
</li>