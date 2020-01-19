<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PdfUrlRepository")
 */
class PdfUrl
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Unique
     */
    private $path;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PdfDocument", mappedBy="pdfUrl")
     */
    private $PdfDocuments;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $expireAt;

    public function __construct()
    {
        $this->PdfDocuments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return Collection|PdfDocument[]
     */
    public function getPdfDocuments(): Collection
    {
        return $this->PdfDocuments;
    }

    public function addPdfDocument(PdfDocument $pdfDocument): self
    {
        if (!$this->PdfDocuments->contains($pdfDocument)) {
            $this->PdfDocuments[] = $pdfDocument;
            $pdfDocument->setPdfUrl($this);
        }

        return $this;
    }

    public function removePdfDocument(PdfDocument $pdfDocument): self
    {
        if ($this->PdfDocuments->contains($pdfDocument)) {
            $this->PdfDocuments->removeElement($pdfDocument);
            // set the owning side to null (unless already changed)
            if ($pdfDocument->getPdfUrl() === $this) {
                $pdfDocument->setPdfUrl(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getExpireAt(): ?\DateTimeInterface
    {
        return $this->expireAt;
    }

    public function setExpireAt(?\DateTimeInterface $expireAt): self
    {
        $this->expireAt = $expireAt;

        return $this;
    }
}
