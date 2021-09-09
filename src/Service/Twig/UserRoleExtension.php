<?php
/**
 * User: demius
 * Date: 09.09.2021
 * Time: 16:08
 */
declare(strict_types=1);

namespace App\Service\Twig;

use App\Security\UserRolesEnum;
use App\Service\ProjectManager;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;


class UserRoleExtension extends AbstractExtension
{
    private TranslatorInterface $translator;
    private ProjectManager $projectManager;

    public function __construct(TranslatorInterface $translator, ProjectManager $projectManager)
    {
        $this->translator = $translator;
        $this->projectManager = $projectManager;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter(
                'role_label',
                [$this, 'roleLabel'],
                ['is_safe' => ['html']]
            )
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'role_label',
                [$this, 'roleLabel'],
                ['is_safe' => ['html']]
            ),
            new TwigFunction(
                'role_label_list',
                [$this, 'roleLabelList'],
                ['is_safe' => ['html']]
            )
        ];
    }

    /**
     * @param UserRolesEnum|string $role
     * @return string
     */
    public function roleLabel($role): string
    {
        if (is_string($role) && UserRolesEnum::isValid($role)) {
            $role = new UserRolesEnum($role);
        }

        if($role instanceof UserRolesEnum) {
            return $this->translator->trans($role->label());
        }

        /**
         *  @var string $projectRole
         *  @var string $projectSuffix
         */
        [$projectRole, $projectSuffix] = UserRolesEnum::explodeSyntheticRole($role);
        if (empty($projectRole) || empty($projectSuffix)) {
            throw new \InvalidArgumentException(<<<'MESSAGE'
    Невозможно интерпретировать роль ' . $role . '. Роль должна или быть списке UserRolesEnum, или быть 
    правильно составленной синтетической ролью ROLE_<NAME>_<PROJECT>
MESSAGE);
        }

        return $this->translator->trans($projectRole)
            . ' ' . $this->translator->trans('role.at_project')
            . ' ' . $this->projectManager->getNameBySuffix($projectSuffix);
    }

    public function roleLabelList(array $roles): string
    {
        $labelList = [];
        foreach ($roles as $role) {
            $labelList[] = $this->roleLabel($role);
        }
        return implode(', ', $labelList);
    }

    /**
     * Returns the name of the extension.
     */
    public function getName(): string
    {
        return 'app_user_role';
    }
}