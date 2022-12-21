<?php

namespace Aimeos\Client\Html\Common\Decorator;

class ListCategories
    extends \Aimeos\Client\Html\Common\Decorator\Base
    implements \Aimeos\Client\Html\Common\Decorator\Iface
{
    public function addData( \Aimeos\MW\View\Iface $view, array &$tags = array(), string &$expire = null ) : \Aimeos\MW\View\Iface
    {
        $view = parent::addData( $view, $tags, $expire );

        if( $text = $view->param( 'f_search' ) )
        {
            $total = 0;
            $cntl = \Aimeos\Controller\Frontend::create( $this->getContext(), 'catalog' );

            foreach( explode( ' ', $text ) as $str )
            {
                $origlen = strlen( $str );
                $str = preg_filter( '/[A-Za-z0-9]/', '$0', $str );

                if( ( $len = strlen( $str ) ) > 0 && $origlen < 4 ) {
                    $cntl->compare( '~=', 'catalog.label', $len === 1 ? ' ' . $str : $str );
                }
            }

            $view->listNodes = $cntl->uses( ['media'] )
                ->compare( '>', 'catalog:relevance("' . str_replace( ['"', ','], ' ', $text ) . '")', 0 )
                ->sort( '-sort:catalog:relevance("' . str_replace( ['"', ','], ' ', $text ) . '")' )->sort( 'catalog.label' )
                ->slice( 0, 100 )->search( $total );
            $view->listNodesTotal = $total;
        }

        return $view;
    }
}
