<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param AggregatedCalendar $calendar
 */
$id = $this->calendar->getId();
$title = $id ? $this->_('aggregatedCalendar_edit') : $this->_('aggregatedCalendar_add');
$aggregation_id = $this->calendar->getAggregation_id();
?>
<section>
    <h1><?= $title; ?></h1>

    <form method="post" action="">
        <fieldset>
            <input type="hidden" name="id" value="<?= $id; ?>" />
            <input type="hidden" name="aggregation_id" value="<?= $aggregation_id; ?>" />

            <?php
                $google_calendar_id = $this->calendar->getGoogle_calendar_id();
                include APPLICATION_HOME.'/blocks/html/aggregations/partials/googleCalendarFields.inc';

                $h = $this->template->getHelper('saveAndCancelButtons');
                echo $h->saveAndCancelButtons(self::generateUri('aggregations.view', ['id'=>$aggregation_id]));
            ?>
        </fieldset>
    </form>
</section>
