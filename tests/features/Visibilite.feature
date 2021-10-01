Feature:
    Проверяем видимость страниц системы для разных ролей Гость, Посетитель, Pm

    Scenario: Гость видит только публичные проекты
        Given I am a guest
         When I am on homepage
         Then the response status code should be 200
          And I should see "pub" in the "section#projects" element
          And I should not see "alice" in the ".project-title" element
         When I am on "/p/pub"
         Then the response status code should be 200
          And I should see "pub" in the ".project-title" element
         When I am on "/p/alice"
         Then the response status code should be 200
          And I should be on "/auth/login"

    Scenario: Алиса видит свой приватный проект
        Given I logged as "Alice"
         When I am on homepage
         Then the response status code should be 200
          And I should see "Alice" in the ".user span" element
          And I should see "pub" in the "section#projects" element
          And I should see "alice" in the "section#projects" element
          And I should not see "bob" in the "section#projects" element
         When I am on "/p/alice"
         Then the response status code should be 200
          And I should see "alice" in the ".project-title" element
         When I am on "/p/bob"
         Then the response status code should be 404
