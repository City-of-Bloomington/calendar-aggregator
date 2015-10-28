<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Aggregation $this->aggregation
 */
$title = $this->aggregation->getId() ? $this->_('aggregation_edit') : $this->_('aggregation_add');
$id = $this->aggregation->getId();
$google_calendar_id = self::escape($this->aggregation->getGoogle_calendar_id());
?>
<section>
    <h1><?= $title; ?></h1>
    <form method="post" action="<?= self::generateUri('aggregations.update'); ?>">
        <fieldset>
            <input name="id" type="hidden" value="<?= $id; ?>" />

            <dl><dt><label  for="google_calendar_id"><?= $this->_('google_calendar_id'); ?></label></dt>
                <dd><input name="google_calendar_id" id="google_calendar_id" value="<?= $google_calendar_id; ?>" /></dd>
            </dl>
            <?php
                $h = $this->template->getHelper('saveAndCancelButtons');
                $uri = $id
                    ? self::generateUri('aggregations.view', ['id'=>$id])
                    : self::generateUri('aggregations.index');
                echo $h->saveAndCancelButtons($uri);
            ?>
        </fieldset>
    </form>
</section>
