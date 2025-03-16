# Circuit: Templating & Routing Made Simple
A modular, in-language, blacklist philosophy, router.

Circuit was a project I began working on after I created "Pulse", it allows for similar functionality seen in "Laravel"
in a more modular form with less bloat. If you are looking for a module that can provide a templating engine and a router
-- with nothing else -- this is likely for you.

Circuit uses a "blacklist" philosophy when it comes to directing traffic. All traffic is routed through your index (or 
another file of your choice) and the URI is interpreted, parsed and acted upon in-language (bypassing the web-server).

The following checks are performed on parsed URIs:

- A missing EndPoint will return a 404
- A mismatched variable type will return a 406
- An incorrect amount of parameters will return a 400

Once the EndPoint is verified you can build logic for how you want it to proceed. Here is an example workflow:

1. Setup:
```
      // Create a Circuit Router and Template parser.
      $router = new Router();
      $template = new Template();
```

2. Define an Endpoint:
```
      // Create an "EndPoint" at "/home" using a 'home.html' template.
      $router
        ->get('/home')
        ->tplFile(
          'home.html',
          // Closure to populate the template's context.
          function (Command $command) {
            return CategoryController::retrieveCatsAndBoards($command);
          }
        );
```

3. Handle the Request:
```
      // Collect the URI and method.
      $uri = $_SERVER['REQUEST_URI'] == '/' ? '/home' : $_SERVER['REQUEST_URI'];
      $met = $_SERVER['REQUEST_METHOD'];
      
      // Validate the endpoint.
      $uriRes = $router->validateEndpoint($uri, $met);
      
      // If the response is bad, serve an error page.
      if ($uriRes['code'] != StatusCodes::OK) {
        serveStatic($uriRes['code']);
      }
```

4. Execute Callbacks and Render the Template:
```
    // Run any potential closures and bind the data to the template context.
    $callbackChk = tryCallback($uriRes);
    
    // If the closure failed, serve an error page.
    if (!$callbackChk) {
      serveStatic($uriRes['code']);
    }
    
    // Tokenize and render the template.
    $template->lex('file', "src/View/layout/{$uriRes['ep']->file");
    $template->render();
```

The system is surprisingly fast at parsing EndPoints, though I have yet to scale it beyond 2 dozen or so. Circuit
also comes with it's own template engine which allows for multiple renders on changing contexts -- while only needing
to tokenize the template once. Circuit's template engine uses a similar syntax to handlebars and makes heavy use of PHP's
blazingly fast associative array access time.

The template engine features the following syntax:
- {{key.value}} - searches for a given key's value inside another key e.g. {{post.content}} where post would be: 'post' => ['content' => 'example]
- {{key.key.value}} - supports nested associative arrays.
- {{for x in|of y}} / {{/for}} - performing loops over nested arrays
- {{`link/to/template/file}} - running templates inside other templates.

Circuit is by no means feature-complete and exists to solve a problem I had __in the moment__ however I do still work on 
it and add more function as and when the need arises.

## What's next?
- Converting the Token array into a Linked List
- Looking at graph tree performance over nested arrays.
- Adding an Abstract Syntax Tree for 'if' statement support
- Storing EndPoints inside of a prefix tree for faster accessing.