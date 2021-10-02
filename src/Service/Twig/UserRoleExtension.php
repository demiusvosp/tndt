<?php
/**
 * User: demius
 * Date: 09.09.2021
 * Time: 16:08
 */
declare(strict_types=1);

namespace App\Service\Twig;

use App\Security\UserRolesEnum;
use App\Service\ProjectContext;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;


class UserRoleExtension extends AbstractExtension
{
    private TranslatorInterface $translator;
    private ProjectContext $projectContext;

    public function __construct(TranslatorInterface $translator, ProjectContext $projectContext)
    {
        $this->translator = $translator;
        $this->projectContext = $projectContext;
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

        [$projectRole, $projectSuffix] = UserRolesEnum::explodeSyntheticRole($role);
        if (empty($projectRole) || empty($projectSuffix)) {
            throw new \InvalidArgumentException("Невозможно интерпретировать роль $role. Роль должна или 
                быть списке UserRolesEnum, или быть правильно составленной синтетической ролью PROLE_<NAME>_<PROJECT>");
        }

        return $this->translator->trans($projectRole)
            . ' ' . $this->translator->trans('role.at_project')
            . ' ' . $this->projectContext->getNameBySuffix($projectSuffix);
    }

    public function roleLabelList(array $roles): string
    {
        /*
         * @TODO возможно стоить группировать этот список по профессиям или проектам, это стоит делать здесь
         */
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