package main.java.com.example.demo;

@Controller
public class HomeController {

    @GetMapping("/")
    public String home() {
        return "index"; 
    }
}
