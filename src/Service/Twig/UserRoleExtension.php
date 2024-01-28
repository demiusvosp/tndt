<?php
/**
 * User: demius
 * Date: 09.09.2021
 * Time: 16:08
 */
declare(strict_types=1);

namespace App\Service\Twig;

use App\Model\Enum\UserPermissionsEnum;
use App\Model\Enum\UserRolesEnum;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;


class UserRoleExtension extends AbstractExtension
{
    private Security $security;
    private TranslatorInterface $translator;
    private UrlGeneratorInterface $router;

    public function __construct(
        Security $security,
        TranslatorInterface $translator,
        UrlGeneratorInterface $router
    ) {
        $this->security = $security;
        $this->router = $router;
        $this->translator = $translator;
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
     * @param \App\Model\Enum\UserRolesEnum|string $role
     * @param bool $html - разрешить html
     * @return string
     */
    public function roleLabel($role, bool $html = true): string
    {
        if(!UserRolesEnum::isProjectRole($role)) {
            return sprintf(
                '%s%s%s',
                $html ? '<b>' : '',
                $this->translator->trans(UserRolesEnum::label($role)),
                $html ? '</b>' : ''
            );
        }

        [$projectRole, $projectSuffix] = UserRolesEnum::explodeSyntheticRole($role);
        if (empty($projectRole) || empty($projectSuffix)) {
            throw new \InvalidArgumentException("Невозможно интерпретировать роль $role. Роль должна или 
                быть в списке UserRolesEnum, или быть правильно составленной синтетической ролью PROLE_<NAME>_<PROJECT>");
        }
        if (! $this->security->isGranted(UserPermissionsEnum::PERM_PROJECT_VIEW, $projectSuffix)) {
            return '';
        }

        return sprintf(
            '%s %s %s',
            $this->translator->trans(UserRolesEnum::label($projectRole)),
            $this->translator->trans('role.at_project'),
            ($html ? $this->getProjectLink($projectSuffix) : $projectSuffix)
        );
    }

    /**
     * @param array $roles
     * @param int|null $length
     * @return string
     */
    public function roleLabelList(array $roles, int $length = null): string
    {
        /*
         * @TODO возможно стоить группировать этот список по профессиям или проектам, это стоит делать здесь
         */
        $labelListLength = 0;
        $labelList = [];
        foreach ($roles as $role) {
            if ($role === UserRolesEnum::ROLE_USER) {
                continue;
            }
            $labelItem = $this->roleLabel($role, true);
            if (!empty($labelItem)) {
                $labelList[] = $labelItem;
                $labelListLength += mb_strlen($this->roleLabel($role, false)) + 2;// текст роли, плюс запятая с пробелом
            }
            if ($length && $labelListLength > $length) {
                array_pop($labelList);
                break;
            }
        }
        if (count($labelList) === 0 ) {
            $labelList[] = $this->roleLabel(UserRolesEnum::ROLE_USER);
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

    /*
     * вобще такая функция намек, что что-то мы делаем не правильно, может весь этот функционал вынести в макросы шаблона, сюда положив поддержку этого макроса, без которой никак
     */
    private function getProjectLink(string $projectSuffix): string
    {
        $link = '<a href="' . $this->router->generate('project.index', ['suffix' => $projectSuffix]) .'" class="invisible_link">';
        return $link . '<b>' . $projectSuffix . '</b></a>';
    }
}