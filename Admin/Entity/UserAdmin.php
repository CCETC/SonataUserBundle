<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\UserBundle\Admin\Entity;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;

use FOS\UserBundle\Model\UserManagerInterface;

class UserAdmin extends Admin
{
    protected $entityIconPath = 'bundles/sonataadmin/famfamfam/user.png';

    protected $formOptions = array(
        'validation_groups' => 'admin'
    );

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('email', null, array('label' => 'E-mail', 'template' => 'SonataUserBundle:User:_list_email.html.twig'))
            //->addIdentifier('name', null, array('label' => 'Name'))
            ->add('groups', 'string', array('label' => 'Groups', 'template' => 'SonataUserBundle:User:_list_groups.html.twig'))
            ->add('enabled', null, array('label' => 'Approved?'))
        ;

        if ($this->isGranted('ROLE_ALLOWED_TO_SWITCH')) {
            $listMapper
                ->add('impersonating', 'string', array('template' => 'SonataUserBundle:User:_list_impersonating.html.twig', 'label' => 'Impersonate'))
            ;
        }        
        
        $listMapper->add('_action', 'actions', array(
            'actions' => array(
                'view' => array(),
                'edit' => array(),
                'delete' => array(),
            ),
            'label' => 'Actions'
        ));
    }

    protected function configureDatagridFilters(DatagridMapper $filterMapper)
    {
        $filterMapper
            //->add('name', null, array('label' => 'Name'))
            ->add('email', null, array('label' => 'E-mail'))
            ->add('enabled', null, array('label' => 'Approved?'))
            ->add('groups', null, array('label' => 'Group'))
        ;
    }
    
    public function getBatchActions()
    {
        $actions = parent::getBatchActions();

        $actions['enable'] = array(
            'label' => 'Approve Selected',
            'ask_confirmation' => false
        );

        $actions['disable'] = array(
            'label' => 'Un-Approve Selected',
            'ask_confirmation' => false
        );
        
        return $actions;
    }

    public function configureRoutes(RouteCollection $collection)
    {
        $collection->add('enable', 'enable/{id}');
        $collection->add('disable', 'disable/{id}');
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General')
                //->add('name', null, array('label' => 'Name', 'required' => false))
                ->add('email', null, array('label' => 'E-mail'))
                ->add('plainPassword', 'text', array('required' => false, 'label' => 'Password'))
                ->add('enabled', null, array('required' => false, 'label' => 'Approved?'))
            ->end()
            ->with('Permissions')
                ->add('groups', 'sonata_type_model', array( 'multiple' => true, 'expanded' => true, 'required' => false, 'label' => 'Groups'))

                ->end()
        ;
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $formMapper
                ->with('Permissions')
                    ->add('roles', 'sonata_security_roles', array( 'multiple' => true, 'expanded' => true, 'required' => false, 'label' => 'Roles'))
                ->end()
            ;
        }
    }
    
    protected function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
            //->add('name', null, array('label' => 'Name'))
            ->add('email', null, array('label' => 'E-mail', 'template' => 'SonataUserBundle:User:_show_email.html.twig'))
            ->add('enabled', null, array('label' => 'Approved?'))
            ->add('groups', null, array('label' => 'Groups'))
        ;
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $showMapper
                ->add('roles', null, array('label' => 'Roles', 'template' => 'SonataUserBundle:User:_show_roles.html.twig'))
            ;
        }
    }

    public function preUpdate($user)
    {
        $this->getUserManager()->updateCanonicalFields($user);
        $this->getUserManager()->updatePassword($user);
    }

    public function setUserManager(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    public function getUserManager()
    {
        return $this->userManager;
    }
}