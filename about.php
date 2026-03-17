<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$skills = $pdo->query("SELECT * FROM skills ORDER BY percentage DESC")->fetchAll();

$skillsByCategory = [];
foreach ($skills as $skill) {
    $skillsByCategory[$skill['category']][] = $skill;
}

$pageTitle = 'About';
include 'includes/header.php';
?>

<div class="section-padding" style="padding-top: 120px;">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h2>About <span class="gradient-text">Me</span></h2>
            <p>Learn more about my background, experience, and expertise in networking and automation.</p>
        </div>
        
        <div class="row align-items-center mb-5">
            <div class="col-md-6" data-aos="fade-right">
                <div style="background: var(--gradient); border-radius: 20px; padding: 50px; text-align: center; color: white;">
                    <i class="fas fa-user-tie" style="font-size: 8rem; opacity: 0.5;"></i>
                    <h3 class="mt-4">Farhan Ale</h3>
                    <p class="mb-0">Network & Automation Engineer</p>
                </div>
            </div>
            <div class="col-md-6" data-aos="fade-left">
                <h3>Who Am I?</h3>
                <p class="text-muted mb-4">I'm a passionate Network & Automation Engineer with expertise in MikroTik and modern network automation technologies. With years of experience in designing and implementing robust network infrastructures, I help organizations optimize their network performance and automate repetitive tasks.</p>
                <p class="text-muted mb-4">My approach combines deep technical knowledge with practical problem-solving skills to deliver solutions that are not only efficient but also scalable and maintainable. I stay updated with the latest industry trends and continuously enhance my skill set.</p>
                <div class="row g-3">
                    <div class="col-6">
                        <div class="p-3 bg-white rounded shadow-sm">
                            <i class="fas fa-network-wired text-primary" style="font-size: 2rem;"></i>
                            <h5 class="mt-2">Network Design</h5>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-white rounded shadow-sm">
                            <i class="fas fa-robot text-secondary" style="font-size: 2rem;"></i>
                            <h5 class="mt-2">Automation</h5>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-white rounded shadow-sm">
                            <i class="fas fa-shield-alt text-success" style="font-size: 2rem;"></i>
                            <h5 class="mt-2">Security</h5>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-white rounded shadow-sm">
                            <i class="fas fa-cloud text-info" style="font-size: 2rem;"></i>
                            <h5 class="mt-2">Cloud</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mb-5">
            <div class="col-12">
                <h3 class="text-center mb-4">Experience</h3>
                <div class="timeline">
                    <div class="row align-items-center mb-4" data-aos="fade-up">
                        <div class="col-md-2 text-center">
                            <div class="p-3 rounded-circle bg-white shadow d-inline-block">
                                <i class="fas fa-briefcase text-primary" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                        <div class="col-md-10">
                            <div class="p-4 bg-white rounded shadow-sm">
                                <h5>Senior Network Engineer</h5>
                                <p class="text-muted mb-1">2022 - Present</p>
                                <p class="mb-0">Leading network infrastructure projects, implementing automation solutions, and mentoring junior engineers.</p>
                            </div>
                        </div>
                    </div>
                    <div class="row align-items-center mb-4" data-aos="fade-up">
                        <div class="col-md-2 text-center">
                            <div class="p-3 rounded-circle bg-white shadow d-inline-block">
                                <i class="fas fa-briefcase text-primary" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                        <div class="col-md-10">
                            <div class="p-4 bg-white rounded shadow-sm">
                                <h5>Network Engineer</h5>
                                <p class="text-muted mb-1">2020 - 2022</p>
                                <p class="mb-0">Designed and implemented network solutions for enterprise clients, focusing on MikroTik technologies.</p>
                            </div>
                        </div>
                    </div>
                    <div class="row align-items-center" data-aos="fade-up">
                        <div class="col-md-2 text-center">
                            <div class="p-3 rounded-circle bg-white shadow d-inline-block">
                                <i class="fas fa-graduation-cap text-primary" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                        <div class="col-md-10">
                            <div class="p-4 bg-white rounded shadow-sm">
                                <h5>Computer Science Graduate</h5>
                                <p class="text-muted mb-1">2016 - 2020</p>
                                <p class="mb-0">Bachelor's degree in Computer Science with specialization in Network and Security.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <h3 class="text-center mb-4">My Skills</h3>
                <?php foreach ($skillsByCategory as $category => $categorySkills): ?>
                <div class="mb-5" data-aos="fade-up">
                    <h4 class="mb-4"><?php echo htmlspecialchars($category); ?></h4>
                    <div class="row g-4">
                        <?php foreach ($categorySkills as $skill): ?>
                        <div class="col-md-6">
                            <div class="p-3 bg-white rounded shadow-sm">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0"><?php echo htmlspecialchars($skill['name']); ?></h6>
                                    <span class="badge bg-primary"><?php echo $skill['percentage']; ?>%</span>
                                </div>
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar" role="progressbar" style="width: <?php echo $skill['percentage']; ?>%; background: var(--gradient);" aria-valuenow="<?php echo $skill['percentage']; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
