<?php
/**
 * @author: Walid Aqleh <waleedakleh23@hotmail.com>
 */

defined('BASEURL') OR exit('No direct script access allowed');
?>
<?php if (!isset($data['race'][0]) || count($data['race']) < race_controller::MAX_RUNNING_RACES) { ?>
    <a href="<?php echo BASEURL ?>race/createrace">create race</a>
<?php } ?>
<?php if (isset($data['race'][0])) { ?>
    <br><a href="<?php echo BASEURL ?>race/progress">progress</a>
    <h2>Currently running races</h2>
    <?php foreach ($data['race'] as $race) { ?>
        <h3>race number <?php echo $race['id']; ?>.
            time: <?php echo gmdate("H:i:s", $race['progress_time']); ?></h3>
        <table border="1">
            <tr>
                <th>horse id</th>
                <th>position</th>
                <th>distance covered</th>
                <th>finished in</th>
            </tr>
            <?php foreach ($race['horses'] as $horse) { ?>
                <tr>
                    <td><?php echo $horse->getId() ?></td>
                    <td><?php echo $race['progress_time'] > 0 ? 1 + array_search('horse_' . $horse->getId(), array_keys($race["current_position"])) : 0 ?></td>
                    <td><?php echo $horse->getDistanceCovered() ?></td>
                    <td><?php echo $horse->finishRace() ? gmdate("H:i:s", $horse->getFinishInSeconds()) : '' ?></td>
                </tr>
            <?php } ?>
        </table>
    <?php } ?>
<?php } ?>
<?php if (isset($data['last_5_races'][0])) { ?>
    <h2>The last 5 race results (top 3 positions and times to complete 1500m)</h2>
    <?php foreach ($data['last_5_races'] as $race) { ?>
        <h3>race number <?php echo $race['id']; ?>.
            time: <?php echo $race['finished'] ? gmdate("H:i:s", $race['finish_time']) : gmdate("H:i:s", $race['progress_time']); ?></h3>
        <table border="1">
            <tr>
                <th>horse id</th>
                <th>position</th>
                <th>distance covered</th>
                <th>finished in</th>
            </tr>
            <?php foreach ($race['horses'] as $horse) { ?>
                <tr>
                    <td><?php echo $horse->getId() ?></td>
                    <td><?php echo $horse->getPosition() ?></td>
                    <td><?php echo $horse->getDistanceCovered() ?></td>
                    <td><?php echo gmdate("H:i:s", $horse->getFinishInSeconds()) ?></td>
                </tr>
            <?php } ?>
        </table>
    <?php } ?>
<?php } ?>
<?php if (isset($data['top_horse']['id'])) { ?>
    <h2>The best ever time, and the stats of the horse that generated it</h2>
    <table border="1">
        <tr>
            <th>horse id</th>
            <th>speed</th>
            <th>strength</th>
            <th>endurance</th>
            <th>finished in</th>
        </tr>
        <tr>
            <td><?php echo $data['top_horse']['id'] ?></td>
            <td><?php echo $data['top_horse']['speed'] ?></td>
            <td><?php echo $data['top_horse']['strength'] ?></td>
            <td><?php echo $data['top_horse']['endurance'] ?></td>
            <td><?php echo gmdate("H:i:s", $data['top_horse']['finished_in_seconds']) ?></td>
        </tr>
    </table>
<?php } ?>
