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
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class GroupAdmin extends Admin
{
    protected $entityIconPath = 'bundles/sonataadmin/famfamfam/group.png';
    
    protected $formOptions = array(
        'validation_groups' => 'Registration'
    );

    public function getNewInstance()
    {
        $class = $this->getClass();

        return new $class('', array());
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name', null, array('label' => 'Name'))
            ->add('roles', 'string', array('label' => 'Roles', 'template' => 'SonataUserBundle:Group:_list_roles.html.twig'))
        ;
        
        $listMapper->add('_action', 'actions', array(
            'actions' => array(
                'view' => array(),
                'edit' => array(),
                'delete' => array(),
            ),
            'label' => 'Actions'
        ));

    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name', null, array('label' => 'Name'))
        ;
    }
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', null, array('label' => 'Name'))
            ->add('roles', 'sonata_security_roles', array( 'multiple' => true, 'expanded' => true, 'required' => false, 'label' => 'Roles'))
        ;
    }
    
    protected function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name', null, array('label' => 'Name'))
            ->add('roles', null, array('label' => 'Roles', 'template' => 'SonataUserBundle:Group:_show_roles.html.twig'))
        ;
    }

}
