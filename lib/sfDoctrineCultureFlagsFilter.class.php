<?php

/**
 * sfDoctrineCultureFlagsFilter
 *
 * Adds culture switching links onto your page.
 * For this magic you got to use sfDoctrineRoute
 * and sfDoctrineRecord object with I18n behavoir
 *
 * @package    sfDoctrineCultureFlagsPlugin
 * @subpackage filter
 * @author     Miami <miami@blackcrystal.net>
 * @version    1.0.3
 */
class sfDoctrineCultureFlagsFilter extends sfFilter {

  public function execute($filterChain)
  {

    // Nothing to do before the action

    $filterChain->execute();
    
    /* @var $route sfDoctrineRoute */
    $route = $this->getContext()->getRequest()->getAttribute('sf_route');
    
    $routename = $this->getContext()->getRouting()->getCurrentRouteName();
    $user = $this->getContext()->getUser();
    $currentCulture = $user->getCulture();
    $msg = '';
    
    if (! $route instanceof sfDoctrineRoute) {
        /**
         * For routes other than sfDoctrineRoute we assume that all 
         * the parameters used for the route are not language-dependent 
         * and we just use them in the route without any changes.
         */
        $route_params = $route->getParameters();

        if (!isset($route_params['sf_culture'])) {
            //We ignore routes which are not localized.
            // @todo: Maybe it would be better to provide some "default localized route"

            return;
        }
        
        //we don't need module and action to generate a URL.
        unset($route_params['module'], $route_params['action']);
        
        foreach ($this->getParameterHolder()->getAll() as $culture => $name) {
            $img = image_tag(public_path("/sfDoctrineCultureFlagsPlugin/images/flags/$culture.png"), array('alt' => $name, 'title' => $name));
            $param = ($culture == $currentCulture) ? array('class'=>'active') : null;
            $route_params['sf_culture'] = $culture;
            $msg.= link_to($img, $routename, $route_params, $param);
        }
    } else {
        /* @var $obj sfDoctrineRecord */
        $obj = $route->getObject();
        if (! $obj instanceof sfDoctrineRecord) return;
        $obj->clearRelated();
        // ^ just a trick
        // i don't know why, but without clearRelated
        // link_to returns the same slug for object
        
        foreach ($this->getParameterHolder()->getAll() as $culture => $name)
        {
            $user->setCulture($culture);
            $img = image_tag(public_path("/sfDoctrineCultureFlagsPlugin/images/flags/$culture.png"), array('alt' => $name, 'title' => $name));
            $param = ($culture == $currentCulture) ? array('class'=>'active') : null;
            $msg .= link_to($img, $routename, $obj, $param);
        
        }
        $user->setCulture($currentCulture);
    }

    $response = $this->getContext()->getResponse();
    $response->setContent(str_ireplace('<!-- CultureFlags -->', $msg, $response->getContent()));
  }

}

