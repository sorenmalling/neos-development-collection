<?php
namespace Neos\Neos\Controller\Module\Administration;

/*
 * This file is part of the Neos.Neos package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Configuration\ConfigurationManager;
use Neos\Flow\Configuration\ConfigurationSchemaValidator;
use Neos\Flow\Configuration\Exception\SchemaValidationException;
use Neos\Flow\I18n\Translator;
use Neos\Utility\SchemaGenerator;
use Neos\Neos\Controller\Module\AbstractModuleController;
use Neos\Error\Messages\Message;

/**
 * The Neos Configuration module controller
 */
class ConfigurationController extends AbstractModuleController
{
    /**
     * @Flow\Inject
     * @var ConfigurationManager
     */
    protected $configurationManager;

    /**
     * @Flow\Inject(lazy = false)
     * @var ConfigurationSchemaValidator
     */
    protected $configurationSchemaValidator;

    /**
     * @Flow\Inject
     * @var SchemaGenerator
     */
    protected $schemaGenerator;

    /**
     * @Flow\Inject
     * @var Translator
     */
    protected $translator;

    /**
     * @param string $type
     * @return void
     */
    public function indexAction($type = 'Settings')
    {
        $availableConfigurationTypes = $this->configurationManager->getAvailableConfigurationTypes();
        $this->view->assignMultiple([
            'type' => $type,
            'availableConfigurationTypes' => $availableConfigurationTypes
        ]);

        if (in_array($type, $availableConfigurationTypes)) {
            $this->view->assign('configuration', $this->configurationManager->getConfiguration($type));

            try {
                $this->view->assign('validationResult', $this->configurationSchemaValidator->validate($type));
            } catch (SchemaValidationException $exception) {
                $this->addFlashMessage(
                    htmlspecialchars($exception->getMessage()),
                    $this->translator->translateById('configuration.anErrorOccurredDuringValidationOfTheConfiguration.title', [], null, null, 'Modules', 'Neos.Neos'),
                    Message::SEVERITY_ERROR,
                    [],
                    1412373972
                );
            }
        } else {
            $this->addFlashMessage(
                $this->translator->translateById('configuration.configurationTypeNotFound.body', [], null, null, 'Modules', 'Neos.Neos'),
                '',
                Message::SEVERITY_ERROR,
                [],
                1412373998
            );
        }
    }
}
