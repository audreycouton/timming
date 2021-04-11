<?php

namespace App\Entity;

use App\Repository\ModuleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ModuleRepository::class)
 */
class Module
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $semester;


    /**
     * @ORM\ManyToMany(targetEntity=Teacher::class, mappedBy="modules", cascade={"persist"})
     */
    private $teachers;

    /**
     * @ORM\OneToMany(targetEntity=Task::class, mappedBy="module", cascade={"persist", "remove"})
     */
    private $tasks;

    /**
     * @ORM\ManyToOne(targetEntity=Campain::class, inversedBy="modules", cascade={"persist"})
     */
    private $year;

    public function __construct()
    {
        $this->teachers = new ArrayCollection();
        $this->tasks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
    public function getSemester(): ?string
    {
        return $this->semester;
    }

    public function setSemester(string $semester): self
    {
        $this->semester = $semester;

        return $this;
    }

    public function getYear(): ?campain
    {
        return $this->year;
    }

    public function setYear(?campain $year): self
    {
        $this->year = $year;

        return $this;
    }


    /**
     * @return Collection|Teacher[]
     */
    public function getTeachers(): Collection
    {
        return $this->teachers;
    }

    public function addTeacher(Teacher $teacher): self
    {
        if (!$this->teachers->contains($teacher)) {
            $this->teachers[] = $teacher;
            $teacher->addModule($this);
        }

        return $this;
    }

    public function removeTeacher(Teacher $teacher): self
    {
        if ($this->teachers->removeElement($teacher)) {
            $teacher->removeModule($this);
        }

        return $this;
    }
    // public function setTeachers(array $teachers)
    // {
    //     $this->teachers = $teachers;
    // }
    /**
     * @return Collection|Task[]
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
            $task->setModule($this);
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getModule() === $this) {
                $task->setModule(null);
            }
        }

        return $this;
    }
    public function __toString(): string
    {
        return $this->name;
    }
}