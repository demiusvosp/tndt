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
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;


class UserRoleExtension extends AbstractExtension
{
    private TranslatorInterface $translator;
    private ProjectContext $projectContext;
    private Environment $twig;

    public function __construct(TranslatorInterface $translator, ProjectContext $projectContext, Environment $twig)
    {
        $this->translator = $translator;
        $this->projectContext = $projectContext;
        $this->twig = $twig;
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
     * @param bool $html - разрешить html
     * @return string
     */
    public function roleLabel($role, $html = true): string
    {
        if(!UserRolesEnum::isProjectRole($role)) {
            return $this->translator->trans(UserRolesEnum::label($role));
        }

        [$projectRole, $projectSuffix] = UserRolesEnum::explodeSyntheticRole($role);
        if (empty($projectRole) || empty($projectSuffix)) {
            throw new \InvalidArgumentException("Невозможно интерпретировать роль $role. Роль должна или 
                быть списке UserRolesEnum, или быть правильно составленной синтетической ролью PROLE_<NAME>_<PROJECT>");
        }


        return $this->translator->trans(UserRolesEnum::label($projectRole))
            . ' ' . $this->translator->trans('role.at_project')
            . ' ' . ($html ? $this->getProjectLink($projectSuffix) : $projectSuffix);
    }

    /**
     * @param array $roles
     * @param int|null $limit
     * @return string
     */
    public function roleLabelList(array $roles, ?int $limit = null): string
    {
        /*
         * @TODO возможно стоить группировать этот список по профессиям или проектам, это стоит делать здесь
         */
        $labelList = [];
        foreach ($roles as $role) {
            //@TODO
            $labelList[] = $this->roleLabel($role, true);
            if ($limit && count($roles) >= $limit) {
                break;
            }
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
        $link = '<a href="{{ path(\'project.index\', {\'suffix\': '.$projectSuffix.'}) }}" class="invisible_link">';
        return $link . '<b>' . $projectSuffix . '</b></a>';
    }
}