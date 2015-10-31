<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Aggregation $this->aggregation
 */
use Application\Models\Person;

$name = self::escape($this->aggregation->getName());
$google_calendar_id = self::escape($this->aggregation->getGoogle_calendar_id());

$helper = $this->template->getHelper('buttonLink');
$editButton   = '';
$deleteButton = '';
if (Person::isAllowed('aggregations', 'update')) {
    $editButton = $helper->buttonLink(
        self::generateUri('aggregations.update', ['id'=>$this->aggregation->getId()]),
        $this->_('aggregation_edit'),
        'edit'
    );
}
if (Person::isAllowed('aggregations', 'delete')) {
    $deleteButton = $helper->buttonLink(
        self::generateUri('aggregations.delete', ['id'=>$this->aggregation->getId()]),
        $this->_('aggregation_delete'),
        'delete'
    );
}
?>
<section>
    <div class="tools"><?= "$editButton $deleteButton"; ?></div>
    <iframe src="//www.google.com/calendar/embed?src=<?= $google_calendar_id; ?>" style="border: 0" width="700" height="600" frameborder="0" scrolling="no"></iframe>
</section>