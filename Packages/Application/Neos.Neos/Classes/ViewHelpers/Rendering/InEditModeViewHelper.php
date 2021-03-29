<?php
namespace Neos\Neos\ViewHelpers\Rendering;

/*
 * This file is part of the Neos.Neos package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\ContentRepository\Domain\Model\NodeInterface;

/**
 * ViewHelper to find out if Neos is rendering an edit mode.
 *
 * = Examples =
 *
 * Given we are currently in an editing mode:
 *
 * <code title="Basic usage">
 * <f:if condition="{neos:rendering.inEditMode()}">
 *   <f:then>
 *     Shown for editing.
 *   </f:then>
 *   <f:else>
 *     Shown elsewhere (preview mode or not in backend).
 *   </f:else>
 * </f:if>
 * </code>
 * <output>
 * Shown for editing.
 * </output>
 *
 *
 * Given we are in the editing mode named "inPlace"
 *
 * <code title="Advanced usage">
 *
 * <f:if condition="{neos:rendering.inEditMode(mode: 'rawContent')}">
 *   <f:then>
 *     Shown just for rawContent editing mode.
 *   </f:then>
 *   <f:else>
 *     Shown in all other cases.
 *   </f:else>
 * </f:if>
 * </code>
 * <output>
 * Shown in all other cases.
 * </output>
 */
class InEditModeViewHelper extends AbstractRenderingStateViewHelper
{
    /**
     * Initialize the arguments.
     *
     * @return void
     * @throws \Neos\FluidAdaptor\Core\ViewHelper\Exception
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('node', NodeInterface::class, 'Optional Node to use context from');
        $this->registerArgument('mode', 'string', 'Optional rendering mode name to check if this specific mode is active');
    }

    /**
     * @return boolean
     * @throws \Neos\Neos\Exception
     * @throws \Neos\FluidAdaptor\Core\ViewHelper\Exception
     */
    public function render(): bool
    {
        $context = $this->getNodeContext($this->arguments['node']);
        $renderingMode = $context->getCurrentRenderingMode();
        if ($this->arguments['mode'] !== null) {
            $result = ($renderingMode->getName() === $this->arguments['mode']) && $renderingMode->isEdit();
        } else {
            $result = $renderingMode->isEdit();
        }

        return $result;
    }
}
