package main.java.com.example.demo;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.jdbc.core.BeanPropertyRowMapper;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestParam;

@Controller
public class OwnerController {

    @Autowired
    private JdbcTemplate jdbcTemplate;

    // Menampilkan halaman login
    @GetMapping("/owner")
    public String owner() {
        return "owner"; // Menampilkan form login di halaman owner
    }

    // Memproses login di halaman owner (POST request)
    @PostMapping("/owner")
    public String ownerLogin(@RequestParam("username") String username,
                             @RequestParam("password") String password, Model model) {
        // Query untuk mencari data owner berdasarkan username
        String query = "SELECT * FROM Manpro WHERE username = ?";

        try {
            // Mengambil data owner berdasarkan username
            Owner owner = jdbcTemplate.queryForObject(query, new Object[]{username},
                    new BeanPropertyRowMapper<>(Owner.class));

            // Validasi password
            if (owner != null && owner.getPassword().equals(password)) {
                // Jika password cocok, login berhasil, redirect ke halaman home
                return "redirect:/homeOwner"; // Arahkan ke halaman home untuk owner
            } else {
                // Jika password salah
                model.addAttribute("error", "Password salah!");
                return "owner"; // Kembali ke halaman login dengan pesan error
            }
        } catch (Exception e) {
            // Jika username tidak ditemukan
            model.addAttribute("error", "Username tidak ditemukan!");
            return "owner"; // Kembali ke halaman login dengan pesan error
        }
    }
}
