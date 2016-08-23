<?php

namespace Dosi\TheseFondationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="document")
 * @ORM\Entity
 */
class Document
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    public $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    public $hiddenName;

    /**
     * @Assert\File( maxSize="10M", mimeTypes={"application/pdf", "image/png"} )
     */
    public $file;


    public function getWebPath()
    {
        return "/".$this->getUploadDir().'/'.$this->hiddenName;
    }

    protected function getUploadRootDir()
    {
        // le chemin absolu du répertoire où les documents uploadés doivent être sauvegardés
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // on se débarrasse de « __DIR__ » afin de ne pas avoir de problème lorsqu'on affiche
        // le document/image dans la vue.
        return 'uploads/documents';
    }

    public function getAbsolutePath()
    {
        return $this->getUploadRootDir();
    }

    public function upload()
    {
        if (null === $this->file) {
            return;
        }

        $name = $this->file->getClientOriginalName();
        $hiddenname = sha1(uniqid(mt_rand(), true)).".".$this->file->guessExtension();

        $this->file->move($this->getUploadRootDir(), $hiddenname);

        $this->name = $name;
        $this->hiddenName = $hiddenname;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Document
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return Document
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set hiddenName
     *
     * @param string $hiddenName
     * @return Document
     */
    public function setHiddenName($hiddenName)
    {
        $this->hiddenName = $hiddenName;

        return $this;
    }

    /**
     * Get hiddenName
     *
     * @return string 
     */
    public function getHiddenName()
    {
        return $this->hiddenName;
    }
}
