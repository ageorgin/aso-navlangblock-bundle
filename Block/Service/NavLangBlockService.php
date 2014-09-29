<?php

namespace Aso\MassEvents\NavLangBlockBundle\Block\Service;

use Sonata\PageBundle\CmsManager\CmsManagerInterface;
use Sonata\PageBundle\Exception\PageNotFoundException;
use Sonata\PageBundle\Model\SiteManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Sonata\AdminBundle\Validator\ErrorElement;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\AdminBundle\Form\FormMapper;

use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\BaseBlockService;

/**
 * Description of NavLangBlockService
 *
 * @author Administrateur
 */
class NavLangBlockService extends BaseBlockService
{
    private $cmsManager;
    private $siteManager;

    /**
     * @param string $name
     * @param EngineInterface $templating
     */
    public function __construct($name, EngineInterface $templating,CmsManagerInterface $cmsManager,SiteManagerInterface $siteManager)
    {
        parent::__construct($name, $templating);
        $this->cmsManager = $cmsManager;
        $this->siteManager = $siteManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'NavLang';
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'template' => 'AsoMassEventsCommonsBundle:Block:block_navLang.html.twig',
            'page' => null,
            'site' => null
        ));
    }
                        
    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function validateBlock(ErrorElement $errorElement, BlockInterface $block)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        // merge settings

        // merge settings
        $settings = $blockContext->getSettings();

        $page = $settings['page'];
        $pages = array();
        if(null != $page) {
            $pageAlias =  $page->getPageAlias();
            $sites = $this->siteManager->findAll();
            foreach($sites as $site) {
                try{
                    $pageRetrieve = $this->cmsManager->getPageByPageAlias($site,$pageAlias);
                    $pages[$site->getName()] = $site->getRelativePath() . $pageRetrieve->getUrl();
                }
                catch(PageNotFoundException $e) {
                    continue;
                }
            }
        }

        return $this->renderResponse($blockContext->getTemplate(), array(
            'block'     => $blockContext->getBlock(),
            'settings'  => $settings,
            'pages' => $pages
        ), $response);
    }
}
