package main.java.com.example.demo;

import lombok.*;

@Data
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class Owner {
    private int id;         // ID owner
    private String username; // Username owner
    private String password; // Password owner
}
