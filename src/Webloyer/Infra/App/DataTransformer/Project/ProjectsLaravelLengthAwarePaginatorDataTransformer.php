<?php

declare(strict_types=1);

namespace Webloyer\Infra\App\DataTransformer\Project;

use Illuminate\Pagination\LengthAwarePaginator;
use Webloyer\App\DataTransformer\Project\{
    ProjectDataTransformer,
    ProjectsDataTransformer,
    ProjectsDtoDataTransformer,
};
use Webloyer\Domain\Model\Project\Projects;

class ProjectsLaravelLengthAwarePaginatorDataTransformer implements ProjectsDataTransformer
{
    /** @var Projects */
    private $projects;
    /** @var ProjectsDtoDataTransformer */
    private $projectsDataTransformer;
    /** @var int */
    private $perPage = 10;
    /** @var int */
    private $currentPage;
    /** @var array<string, string> */
    private $options;

    /**
     * @param ProjectsDtoDataTransformer $projectsDataTransformer
     * @return void
     */
    public function __construct(ProjectsDtoDataTransformer $projectsDataTransformer)
    {
        $this->projectsDataTransformer = $projectsDataTransformer;
        $this->currentPage = LengthAwarePaginator::resolveCurrentPage();
        $this->options = [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ];
    }

    /**
     * @param int $perPage
     * @return self
     */
    public function setPerPage(int $perPage): self
    {
        $this->perPage = $perPage;
        return $this;
    }

    /**
     * @param Projects $projects
     * @return self
     */
    public function write(Projects $projects): self
    {
        $this->projects = $projects;
        return $this;
    }

    /**
     * @return LengthAwarePaginator<object>
     */
    public function read()
    {
        $projects = $this->projectsDataTransformer->write($this->projects)->read();
        return new LengthAwarePaginator(
            array_slice(
                $projects,
                $this->perPage * ($this->currentPage - 1),
                $this->perPage
            ),
            count($projects),
            $this->perPage,
            $this->currentPage,
            $this->options
        );
    }

    /**
     * @return ProjectDataTransformer
     */
    public function projectDataTransformer(): ProjectDataTransformer
    {
        return $this->projectsDataTransformer->projectDataTransformer();
    }
}
