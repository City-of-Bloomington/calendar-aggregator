<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param array $this->aggregations
 */
use Application\Models\Person;
?>
<section>
    <h1><?= $this->_(['aggregation', 'aggregations', count($this->aggregations)]); ?></h1>
	<div class="tools">
	<?php
        if (Person::isAllowed('aggregations', 'add')) {
            $helper = $this->template->getHelper('buttonLink');
            echo $helper->buttonLink(
                self::generateUri('aggregations.update'),
                $this->translate('aggregations_add'),
                'add'
            );
        }
	?>
	</div>
    <table>
        <thead>
            <tr><th><?= $this->_('name'); ?></th>
                <th><?= $this->_('google_calendar_id'); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php
            foreach ($this->aggregations as $a) {
                $name = self::escape($a->getName());
                $google_id = self::escape($a->getGoogle_calendar_id());

                $uri = self::generateUri('aggregations.view', ['id'=>$a->getId()]);

                echo "
                <tr><td><a href=\"$uri\">$name</a></td>
                    <td><a href=\"$uri\">$google_id</a></td>
                </tr>
                ";
            }
        ?>
        </tbody>
    </table>
</section>
