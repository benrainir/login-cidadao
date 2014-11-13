<?php

namespace PROCERGS\LoginCidadao\NotificationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use PROCERGS\OAuthBundle\Entity\Client;
use PROCERGS\LoginCidadao\CoreBundle\Entity\Person;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * Notification Broadcast entity
 *
 * @ORM\Table(name="broadcast")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Broadcast
{

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToMany(targetEntity="PROCERGS\LoginCidadao\CoreBundle\Entity\Person")
     * @ORM\JoinTable(
     *     name="broadcast_person",
     *     joinColumns={@ORM\JoinColumn(name="broadcast_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="personid", referencedColumnName="id")}
     * )
     * @var \Doctrine\Common\Collections\Collection
     */
    private $receivers;

    /**
     * @ORM\ManyToOne(targetEntity="PROCERGS\LoginCidadao\CoreBundle\Entity\Person", inversedBy="broadcasts")
     * @ORM\JoinColumn(name="person_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $person;

    /**
     * @ORM\ManyToOne(targetEntity="PROCERGS\LoginCidadao\NotificationBundle\Entity\Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $category;

    /**
     * @ORM\Column(name="html_tpl", type="text", nullable=true)
     */
    private $htmlTemplate;
    
    /**
     * @ORM\Column(name="title", type="string", nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(name="short_text", type="string", nullable=true)
     */
    private $shortText;
    
    /**
     * @ORM\Column(name="sent", type="boolean", nullable=true)
     */
    private $sent;

    public function getId()
    {
        return $this->id;
    }

    public function setId($var)
    {
        $this->id = $var;
        return $this;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory($var)
    {
        $this->category = $var;
        return $this;
    }

    public function getPerson()
    {
        return $this->person;
    }

    public function setPerson($var)
    {
        $this->person = $var;
        return $this;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        if (!($this->getCreatedAt() instanceof \DateTime)) {
            $this->createdAt = new \DateTime();
        }
    }

    public function getReceivers()
    {
        return $this->receivers;
    }

    public function setReceivers($receivers)
    {
        $this->receivers = $receivers;
        return $this;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getHtmlTemplate()
    {
        return $this->htmlTemplate;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    // public function setHtmlTemplate($htmlTemplate)
    // {
    //     $this->htmlTemplate = $htmlTemplate;
    //     return $this;
    // }

    public function setHtmlTemplate(ArrayCollection $placeholders) {
      $html = $this->getCategory()->getHtmlTemplate();

      $values = array();
      foreach ($placeholders as $placeholder) {
          $name = $placeholder->getName();

          if (array_key_exists($name, $values)) {
              $value = $values[$name];
          } else {
              $value = $placeholder->getValue();
          }
          $html = str_replace($name, $value, $html);
      }
      $this->htmlTemplate = $html;
      return $this;
    }
    
    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }
    
    public function getShortText()
    {
        return $this->shortText;
    }

    public function setShortText($shortText)
    {
        $this->shortText = $shortText;
        return $this;
    }
    
    public function getSent()
    {
        return $this->sent;
    }

    public function setSent($sent)
    {
        $this->sent = $sent;
        return $this;
    }

}
