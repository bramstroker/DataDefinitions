<?php
/**
 * Data Definitions.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2016-2019 w-vision AG (https://www.w-vision.ch)
 * @license    https://github.com/w-vision/DataDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace Wvision\Bundle\DataDefinitionsBundle\Model\ImportDefinition;

use Exception;
use Pimcore\Model;
use Pimcore\Model\AbstractModel;
use Pimcore\Model\Listing\CallableFilterListingInterface;
use Pimcore\Model\Listing\CallableOrderListingInterface;
use Pimcore\Model\Listing\Traits\FilterListingTrait;
use Pimcore\Model\Listing\Traits\OrderListingTrait;
use Wvision\Bundle\DataDefinitionsBundle\Model\ImportDefinitionInterface;

/**
 * @method loadList()
 */
class Listing extends AbstractModel implements CallableFilterListingInterface, CallableOrderListingInterface
{
    use FilterListingTrait;
    use OrderListingTrait;

    /**
     * Contains the results of the list.
     * They are all an instance of Configuration.
     */
    public ?array $definitions = null;

    /**
     * Get Configurations.
     *
     * @return ImportDefinitionInterface[]
     * @throws Exception
     */
    public function getObjects()
    {
        if (null === $this->definitions) {
            $this->loadList();
        }

        return $this->definitions;
    }

    /**
     * Set Definitions.
     *
     * @param array $definitions
     */
    public function setObjects(array $definitions)
    {
        $this->definitions = $definitions;
    }
}
