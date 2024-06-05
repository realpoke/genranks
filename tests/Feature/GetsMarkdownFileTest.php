<?php

test('it converts markdown to HTML', function () {
    $action = new \App\Actions\GetMarkdownFile();

    $tempFilePath = resource_path('markdown/temp_test_1.md');
    $markdownContent = '# Test Heading';
    File::put($tempFilePath, $markdownContent);

    $content = $action('temp_test_1');

    File::delete($tempFilePath);

    $expectedHtml = '<h1>Test Heading</h1>';
    expect($content)->toBeString();
    expect($content)->toContain($expectedHtml);
});

test('it returns the contents of a markdown file', function () {
    $action = new \App\Actions\GetMarkdownFile();

    $tempFilePath = resource_path('markdown/temp_test_2.md');
    $markdownContent = 'This is a test markdown file.';
    File::put($tempFilePath, $markdownContent);

    $content = $action('temp_test_2');

    File::delete($tempFilePath);

    expect($content)->toBeString();
});

test('it returns the contents of a language-based markdown file', function () {
    $action = new \App\Actions\GetMarkdownFile();

    $tempFilePathEn = resource_path('markdown/temp_test_en.md');
    $tempFilePathEs = resource_path('markdown/temp_test_es.md');

    $markdownContentEn = 'This is a test markdown file (English).';
    $markdownContentEs = 'Este es un archivo de prueba (Español).';

    File::put($tempFilePathEn, $markdownContentEn);
    File::put($tempFilePathEs, $markdownContentEs);

    App::setLocale('en');
    $contentEn = $action('temp_test');

    App::setLocale('es');
    $contentEs = $action('temp_test');

    File::delete($tempFilePathEn);
    File::delete($tempFilePathEs);

    expect($contentEn)->toContain('This is a test markdown file (English).');
    expect($contentEs)->toContain('Este es un archivo de prueba (Español).');
});

test('it returns a 404 error when no markdown file is found', function () {
    $action = new \App\Actions\GetMarkdownFile();

    $nonExistentFileName = 'non_existent_file';

    expect(function () use ($action, $nonExistentFileName) {
        $action($nonExistentFileName);
    })->toThrow(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);
});

test('can access the /doc endpoint for conditions', function () {
    $response = $this->get('/doc/conditions');
    $response->assertStatus(200);
});

test('can access the /doc endpoint for policy', function () {
    $response = $this->get('/doc/policy');
    $response->assertStatus(200);
});

test('can access the /doc endpoint for service', function () {
    $response = $this->get('/doc/service');
    $response->assertStatus(200);
});
