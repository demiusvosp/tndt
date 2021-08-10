<?php
/**
 * User: demius
 * Date: 06.02.20
 * Time: 23:59
 */

namespace App\Controller;


use App\Entity\Project;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;


class DashboardController extends AbstractController
{

    public function index(Request $request)
    {
        $projects = [
            (new Project())->setSuffix('ABC')->setName('Первый проект')->setDescription('Захворавшие выезжают тогда поскорей со своими болезнями и чемоданами на  разные  курорты  и  побережья  в  поисках  своей  утраченной молодости.  Они  купаются  в  море, ныряют  и  плавают,  валяются  часами  на  самом  ужасном  солнцепеке,  шляются по горам и пьют  специальные и слабительные  воды.  Еще  более от этого хворают и с почтением взирают на врачей, ожидая от них чудес,  возвращения потерянных сил и восстановления утраченных соков.'),
            (new Project())->setSuffix('PRJ')->setName('Второй проект')->setDescription('Французская поговорка гласит: "Сухой рыбак и мокрый охотник являют видпечальный". Не имев никогда пристрастия к рыбной ловле, я не могу судить отом, что испытывает рыбак в хорошую, ясную погоду и насколько в ненастноевремя удовольствие, доставляемое ему обильной добычей, перевешиваетнеприятность быть мокрым. Но для охотника дождь - сущее бедствие. Именнотакому бедствию подверглись мы с Ермолаем в одну из наших поездок затетеревами в Белевский уезд.'),
            (new Project())->setSuffix('LTN')->setName('Another project')->setDescription('Haec nuntiant domum Albani. Et bellum utrimque summa ope parabatur, civili simillimum bello, prope inter parentes natosque, Troianam utramque prolem, cum Lavinium ab Troia, ab Lavinio Alba, ab Albanorum stirpe regum oriundi Romani essent. Euentus tamen belli minus miserabilem dimicationem fecit, quod nec acie certatum est et tectis modo dirutis alterius urbis duo populi in unum confusi sunt. Albani priores ingenti exercitu in agrum Romanum impetum fecere.'),
        ];
        return $this->render('dashboard/index.html.twig', ['projects' => $projects]);
    }
}