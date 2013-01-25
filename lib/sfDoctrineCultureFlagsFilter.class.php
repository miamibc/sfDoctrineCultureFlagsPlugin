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
    
    $route = $this->getContext()->getRequest()->getAttribute('sf_route');
    if (! $route instanceof sfDoctrineRoute) return;
    $obj = $route->getObject();
    if (! $obj instanceof sfDoctrineRecord) return;
    $obj->clearRelated();
    // ^ just a trick
    // i don't know why, but without clearRelated
    // link_to returns the same slug for object

    /**
     * If you use localised parameters from related objects you need to
     * manually clear the relations of those relations (sounds crazy!)
     * @see README section "Note on complex routes" for more details.
     * e.g.
     *     if ($obj instanceof Product) {
     *         $obj->Category->clearRelated();
     *     }
     */
    
    /* @var $route sfDoctrineRoute */
    /* @var $obj sfDoctrineRecord */

    $routename = $this->getContext()->getRouting()->getCurrentRouteName();
    $user = $this->getContext()->getUser();
    $currentCulture = $user->getCulture();
    $msg = '';
    foreach ($this->getParameterHolder()->getAll() as $culture => $name)
    {
      $user->setCulture($culture);
      $img = image_tag(public_path("/sfDoctrineCultureFlagsPlugin/images/flags/$culture.png"), array('alt' => $name, 'title' => $name));
      $param = ($culture == $currentCulture) ? array('class'=>'active') : null;
      $msg .= link_to($img, $routename, $obj, $param);
      
    }
    $user->setCulture($currentCulture);

    $response = $this->getContext()->getResponse();
    $response->setContent(str_ireplace('<!-- CultureFlags -->', $msg, $response->getContent()));
  }

}

