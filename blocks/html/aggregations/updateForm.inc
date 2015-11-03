<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Aggregation $this->aggregation
 */
$title = $this->aggregation->getId() ? $this->_('aggregation_edit') : $this->_('aggregation_add');
$id = $this->aggregation->getId();
?>
<section>
    <h1><?= $title; ?></h1>
    <form method="post" action="<?= self::generateUri('aggregations.update'); ?>">
        <fieldset>
            <input name="id" type="hidden" value="<?= $id; ?>" />

            <?php
                $google_calendar_id = self::escape($this->aggregation->getGoogle_calendar_id());
                include __DIR__.'/partials/googleCalendarFields.inc';

                $h = $this->template->getHelper('saveAndCancelButtons');
                $uri = $id
                    ? self::generateUri('aggregations.view', ['id'=>$id])
                    : self::generateUri('aggregations.index');
                echo $h->saveAndCancelButtons($uri);
            ?>
        </fieldset>
    </form>
</section>
