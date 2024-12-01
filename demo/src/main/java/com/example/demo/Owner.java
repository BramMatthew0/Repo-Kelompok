package com.example.demo;
import lombok.*;

@Data
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class Owner {
    private String username;   // Nama pegawai
    private String password; // Password pegawai
}