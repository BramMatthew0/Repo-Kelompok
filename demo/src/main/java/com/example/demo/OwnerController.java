package main.java.com.example.demo;

import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestParam;

@Controller
public class OwnerController {

    // Show the login page
    @GetMapping("/login")
    public String showLoginPage() {
        return "LoginOwner"; // Refers to LoginOwner.html in src/main/resources/templates
    }

    // Handle login form submission
    @PostMapping("/login")
    public String handleLogin(@RequestParam("username") String username,
                              @RequestParam("password") String password, 
                              Model model) {
        // Simulate validation (replace with real database or service validation)
        if (isValidOwner(username, password)) {
            model.addAttribute("username", username);
            return "redirect:/home"; // Redirect to home page after successful login
        } else {
            model.addAttribute("error", "Invalid username or password");
            return "LoginOwner"; // Show login page again on failure
        }
    }

    // Show the home page after login
    @GetMapping("/home")
    public String showHomePage(Model model) {
        model.addAttribute("welcomeMessage", "Welcome to the Owner Home Page!");
        return "HomePageOwnerAfterLogin"; // Refers to HomePageOwnerAfterLogin.html
    }

    // Simulated method to validate owner credentials
    private boolean isValidOwner(String username, String password) {
        // Replace this logic with actual authentication logic (e.g., database check)
        return "owner@example.com".equals(username) && "password123".equals(password);
    }
}