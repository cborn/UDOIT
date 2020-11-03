<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\IssueRepository")
 */
class Issue implements \JsonSerializable
{
    static $issueError = 'error';
    static $issueSuggestion = 'suggestion';

    // Private Members
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ContentItem", inversedBy="issues")
     * @ORM\JoinColumn(nullable=false)
     */
    private $contentItem;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $scanRuleId;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $html;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Report", inversedBy="issues")
     */
    private $reports;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private $fixedBy;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fixedOn;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $previewHtml;


    // Constructor
    public function __construct()
    {
        $this->reports = new ArrayCollection();
    }


    // Public Methods

    /**
     * Serializes Issue into JSON.
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return [
            "id" => $this->id,
            "status" => $this->status,
            "contentItemId" => $this->contentItem->getId(),
            "scanRuleId" => $this->scanRuleId,
            "type" => $this->type,
            "sourceHtml" => $this->html,
            "previewHtml" => $this->previewHtml,
        ];
    }

    // Getters and Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContentItem(): ?ContentItem
    {
        return $this->contentItem;
    }

    public function setContentItem(?ContentItem $contentItem): self
    {
        $this->contentItem = $contentItem;

        return $this;
    }

    public function getScanRuleId(): ?string
    {
        return $this->scanRuleId;
    }

    public function setScanRuleId(string $scanRuleId): self
    {
        $this->scanRuleId = $scanRuleId;

        return $this;
    }

    public function getHtml(): ?string
    {
        return $this->html;
    }

    public function setHtml(?string $html): self
    {
        $this->html = $html;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|Report[]
     */
    public function getReports(): Collection
    {
        return $this->reports;
    }

    public function addReport(Report $report): self
    {
        if (!$this->reports->contains($report)) {
            $this->reports[] = $report;
        }

        return $this;
    }

    public function removeReport(Report $report): self
    {
        if ($this->reports->contains($report)) {
            $this->reports->removeElement($report);
        }

        return $this;
    }

    public function getFixedBy(): ?User
    {
        return $this->fixedBy;
    }

    public function setFixedBy(?User $fixedBy): self
    {
        $this->fixedBy = $fixedBy;

        return $this;
    }

    public function getFixedOn(): ?\DateTimeInterface
    {
        return $this->fixedOn;
    }

    public function setFixedOn(?\DateTimeInterface $fixedOn): self
    {
        $this->fixedOn = $fixedOn;

        return $this;
    }

    public function getPreviewHtml(): ?string
    {
        return $this->previewHtml;
    }

    public function setPreviewHtml(?string $previewHtml): self
    {
        $this->previewHtml = $previewHtml;

        return $this;
    }
}